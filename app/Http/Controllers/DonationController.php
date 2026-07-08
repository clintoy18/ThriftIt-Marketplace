<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\SubmitProofAction as SubmitProofRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Models\Barangay;
use App\Models\Categories;
use App\Models\Comment;
use App\Models\Donation;
use App\Models\DonationImage;
use App\Services\DonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DonationController extends Controller
{
    protected $donationService;

    public function __construct(DonationService $donationService)
    {
        $this->donationService = $donationService;
    }

    public function index(): View
    {
        // 1. Get ALL donations for this user
        $allDonations = $this->donationService->getDonationsByUser(Auth::id());

        // 2. Filter the collection into groups
        $approved = $allDonations->where('approval_status', 'approved');
        $pending = $allDonations->where('approval_status', 'pending');
        $rejected = $allDonations->where('approval_status', 'rejected');

        // 3. Pass separated lists to the view
        return view('donations.index', [
            'approved' => $approved,
            'pending' => $pending,
            'rejected' => $rejected,
        ]);
    }

    public function create()
    {
        $categories = Categories::all();
        $barangays = Barangay::all();

        return view('donations.create', compact('categories', 'barangays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDonationRequest $request)
    {
        // Validate request data
        $validated = $request->validated();

        // Attach authenticated user ID
        $validated['user_id'] = Auth::id();
        $validated['approval_status'] = Auth::user()->is_verified ? 'approved' : 'pending';

        // Handle images safely (if any)
        $images = $request->hasFile('images') ? $request->file('images') : [];

        // Create donation via service layer
        $this->donationService->createDonation($validated, $images);

        // Redirect with success message
        return redirect()
            ->route('donations.index')
            ->with('success', 'Donation created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        Cache::forget("donation_{$id}_comments");
        Cache::forget("donation_{$id}_with_comments");

        $donation = Donation::with(['user', 'category', 'donationImages'])->findOrFail($id);
        $allComments = Comment::with(['user'])
            ->where('donation_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        $byParent = $allComments->groupBy('parent_id');
        $topLevel = $byParent->get(null, collect())->sortByDesc('created_at')->values();

        $topLevel->each(function ($root) use ($byParent) {
            $flatReplies = collect();
            $stack = [];
            foreach ($byParent->get($root->id, collect()) as $child) {
                $flatReplies->push($child);
                $stack[] = $child;
            }
            while (! empty($stack)) {
                $node = array_pop($stack);
                foreach ($byParent->get($node->id, collect()) as $child) {
                    $flatReplies->push($child);
                    $stack[] = $child;
                }
            }
            $root->setRelation('replies', $flatReplies);
        });

        $donation->setRelation('comments', $topLevel);

        $moreDonations = $this->donationService->getMoreDonationsByUser($donation->user_id, $donation->id);

        return response()
            ->view('donations.show', compact('donation', 'moreDonations'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('Last-Modified', gmdate('D, d M Y H:i:s').' GMT')
            ->header('ETag', md5(serialize($donation->comments)));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $donation = $this->donationService->getDonationById($id);
        $this->authorize('update', $donation);

        $categories = Categories::all();

        return view('donations.edit', ['donation' => $donation, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDonationRequest $request, Donation $donation)
    {
        $this->authorize('update', $donation);

        // 2️⃣ Validate request
        $validated = $request->validated();

        // --------------------------------------------
        // LOGIC: If the donation was rejected, editing it resets status to 'pending' for review.
        if ($donation->approval_status === 'rejected') {
            $validated['approval_status'] = 'pending';
        }
        // --------------------------------------------

        // 3️⃣ Prepare images array for service
        $images = [
            'main' => $request->file('image'),       // Main image (if applicable)
            'gallery' => $request->file('images', []), // Gallery images
        ];

        // 4️⃣ Call service to handle update including S3 uploads
        $this->donationService->updateDonation($donation, $validated, $images);

        // 5️⃣ Handle deletion of gallery images if any
        $deleteIds = collect($request->input('deletedImages', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (! empty($deleteIds)) {
            $imagesToDelete = $donation->donationImages()->whereIn('id', $deleteIds)->get(['id', 'image']);
            foreach ($imagesToDelete as $img) {
                if ($img->image && Storage::disk('s3')->exists($img->image)) {
                    Storage::disk('s3')->delete($img->image);
                }
            }
            DonationImage::where('donation_id', $donation->id)->whereIn('id', $deleteIds)->delete();
        }

        // 6️⃣ Determine redirect message based on previous status
        $message = ($donation->approval_status === 'rejected')
            ? 'Donation updated and resubmitted for approval!'
            : 'Donation updated successfully!';

        return redirect()->route('donations.show', $donation)
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $donation = $this->donationService->getDonationById($id);
        $this->authorize('delete', $donation);

        $donation->delete();

        return redirect()->route('donations.index')->with('success', 'Donation deleted successfully!');
    }

    public function getAllDonations()
    {
        // Optional filters from query string
        $categoryId = request('category');
        $barangayId = request('barangay');

        $donationsQuery = Donation::where('approval_status', 'approved')
            ->where('status', 'available')
            ->with(['donationImages', 'category', 'barangay']);

        if ($categoryId) {
            $donationsQuery->where('category_id', $categoryId);
        }

        if ($barangayId) {
            $donationsQuery->where('barangay_id', $barangayId);
        }

        $donations = $donationsQuery->paginate(12);

        // Data for filters
        $categories = Categories::all();
        $barangays = Barangay::all();

        return view('donations.donation-hub', compact(
            'donations',
            'categories',
            'barangays',
            'categoryId',
            'barangayId'
        ));
    }

    public function markAsDonated(SubmitProofRequest $request, Donation $donation): RedirectResponse
    {
        $this->authorize('markAsDonated', $donation);

        // Pass the proof file to the service (not stored here)
        if ($donation->status === 'donated') {
            return redirect()->route('donations.show', $donation->id)
                ->with('error', 'This item is already donated and cannot be edited.');
        }

        $this->donationService->updateDonation(
            $donation,
            ['verification_status' => 'pending'],
            ['proof' => $request->file('proof')]
        );

        return redirect()
            ->route('donations.index')
            ->with('success', 'Proof submitted successfully! Awaiting admin verification to redeem points.');
    }
}
