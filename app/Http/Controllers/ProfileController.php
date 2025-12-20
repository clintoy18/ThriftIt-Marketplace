<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Barangay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;



class ProfileController extends Controller
{



    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {

        $user = $request->user(); // logged-in user only
        $barangays = Barangay::all();

        $totalListings = $user->products()->where('approval_status', 'approved')->count();
        $itemsSold = $user->products()->where('status', 'sold')->count();
        $revenue = $user->products()->where('status', 'sold')->sum('price');
        $itemsDonated = $user->donations()->where('status', 'donated')->count();
        // $unreadMessages = $user->receivedMessages()->where('is_read', false)->count();

        return view('profile.edit', compact(
            'user',
            'totalListings',
            'itemsSold',
            'revenue',
            'itemsDonated',
            'barangays'
        ));
    }

    /**
     * Display the user's password update form.
     */
    public function edit1(Request $request): View
    {
        $user = $request->user();
        return view('profile.edit1', compact('user'));
    }

    /**
     * Display the user's data & privacy settings.
     */
    public function edit2(Request $request): View
    {
        $user = $request->user();
        return view('profile.edit2', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Validate all form inputs
        $validated = $request->validated();

        // ✅ Handle S3 profile picture upload
        if ($request->hasFile('profile_pic')) {
            // Delete old image if it exists in S3
            if ($user->profile_pic && Storage::disk('s3')->exists($user->profile_pic)) {
                Storage::disk('s3')->delete($user->profile_pic);
            }

            // Store new profile picture in S3
            $path = $request->file('profile_pic')->store('profile_pictures', 's3');

            Storage::disk('s3')->setVisibility($path, 'public');

            $validated['profile_pic'] = $path;
        }

        // ✅ Update Barangay if provided
        if ($request->filled('barangay_id')) {
            $validated['barangay_id'] = $request->barangay_id;
        }

        // ✅ Reset email verification if changed
        if ($user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
        }

        // ✅ Apply updates to user
        $user->update($validated);

        return Redirect::route('profile.edit')->with('status', 'Profile updated successfully!');
    }




public function show(User $user)
{
    // 1. Fetch Featured Buyers with their Items (Eager Loaded) - Paginated (3 per page)
    $featuredBuyers = \App\Models\FeaturedBuyer::with('items')
        ->where('user_id', $user->id)
        ->latest()
        ->paginate(3);

    // 2. Available products (approved, not sold)
    $availableProducts = $user->products()
        ->where('approval_status', 'approved')
        ->where('status', '!=', 'sold')
        ->paginate(8);

    // 3. Sold products
    $soldProducts = $user->products()->where('status', 'sold')->paginate(8);

    // 4. Orders received for this user's products
    $orders = $user->ordersAsSeller()->with(['product', 'buyer'])->get();

    // 5. Orders placed by the authenticated user (as buyer)
    $buyerOrders = collect();
    if (Auth::check()) {
        $buyerOrders = Auth::user()->orders()
            ->with(['product.images'])
            ->latest()
            ->get();
    }

    // 6. Donations created by this user
    $donations = $user->donations()->with(['category', 'donationImages'])->latest()->get();

    // 7. Works (approved only)
    $works = $user->works()->where('approval_status', 'approved')->get();

    // 8. Completed appointments
    $completedAppointments = $user->appointments()
        ->where('appstatus', 'completed')
        ->with(['upcycler'])
        ->get();

    $completedAppointmentsAsUpcycler = $user->appointmentsAsUpcycler()
        ->where('appstatus', 'completed')
        ->with(['upcycler'])
        ->get();

    // 9. Dashboard statistics
    $totalListings = $user->products()->where('approval_status', 'approved')->count();
    $itemsSold = $user->products()->where('status', 'sold')->count();
    $revenue = $user->products()->where('status', 'sold')->sum('price');
    $itemsDonated = $user->donations()->where('status', 'donated')->count();
    $approvedWorks = $user->works()->where('approval_status', 'approved')->count();
    $completedAppointmentsCount = $completedAppointments->count();
    $completedAppointmentsAsUpcyclerCount = $completedAppointmentsAsUpcycler->count();

    return view('profile.show', [
        'user' => $user,
        'featuredBuyers' => $featuredBuyers, // <-- Added this
        'availableProducts' => $availableProducts,
        'soldProducts' => $soldProducts,
        'orders' => $orders,
        'buyerOrders' => $buyerOrders,
        'donations' => $donations,
        'totalListings' => $totalListings,
        'itemsSold' => $itemsSold,
        'revenue' => $revenue,
        'itemsDonated' => $itemsDonated,
        'works' => $works,
        'approvedWorks' => $approvedWorks,
        'completedAppointments' => $completedAppointments,
        'completedAppointmentsCount' => $completedAppointmentsCount,
        'completedAppointmentsAsUpcycler' => $completedAppointmentsAsUpcycler,
        'completedAppointmentsAsUpcyclerCount' => $completedAppointmentsAsUpcyclerCount,
    ]);
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function uploadVerificationDocument(Request $request)
    {
        $user = $request->user();

        // Check if profile information is complete
        $missingFields = [];
        if (empty($user->fname)) {
            $missingFields[] = 'First Name';
        }
        if (empty($user->lname)) {
            $missingFields[] = 'Last Name';
        }
        if (empty($user->email)) {
            $missingFields[] = 'Email';
        }
        if (empty($user->barangay_id)) {
            $missingFields[] = 'Barangay';
        }

        if (!empty($missingFields)) {
            return back()->withErrors([
                'profile_incomplete' => 'Please complete your profile information before submitting verification documents. Missing: ' . implode(', ', $missingFields)
            ])->withInput();
        }

        $request->validate([
            'verification_document'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'verification_document_back' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'terms' => 'required|accepted',
        ], [
            'terms.required' => 'You must agree to the Terms and Conditions to proceed.',
            'terms.accepted' => 'You must agree to the Terms and Conditions to proceed.',
        ]);

        $dataToUpdate = [
            'verification_status' => 'pending',
        ];

        // 1. Handle Front Image
        if ($request->hasFile('verification_document')) {
            $pathFront = $request->file('verification_document')->store('verification-documents', 's3');
            Storage::disk('s3')->setVisibility($pathFront, 'public');

            $dataToUpdate['verification_document'] = $pathFront;
        }

        // 2. Handle Back Image
        if ($request->hasFile('verification_document_back')) {
            $pathBack = $request->file('verification_document_back')->store('verification-documents', 's3');
            Storage::disk('s3')->setVisibility($pathBack, 'public');

            $dataToUpdate['verification_document_back'] = $pathBack;
        }

        // 3. Save changes
        $user->update($dataToUpdate);

        return back()->with('status', 'Verification documents uploaded successfully and sent for review.');
    }

    /**
     * Export dashboard data as PDF
     */
    public function exportDashboardPdf(Request $request)
    {
        $user = $request->user();

        // Dashboard statistics
        $totalListings = $user->products()->where('approval_status', 'approved')->count();
        $itemsSold = $user->products()->where('status', 'sold')->count();
        $revenue = $user->products()->where('status', 'sold')->sum('price');
        $itemsDonated = $user->donations()->where('status', 'donated')->count();
        $approvedWorks = $user->works()->where('approval_status', 'approved')->count();
        $completedAppointmentsCount = $user->appointments()->where('appstatus', 'completed')->count();
        $completedAppointmentsAsUpcyclerCount = $user->appointmentsAsUpcycler()->where('appstatus', 'completed')->count();

        // Recent sold products
        $soldProducts = $user->products()->where('status', 'sold')->latest()->take(10)->get();

        // Recent donations
        $donations = $user->donations()->where('status', 'donated')->latest()->take(10)->get();

        $isUpcycler = $user->isUpcycler();

        $data = compact(
            'user',
            'totalListings',
            'itemsSold',
            'revenue',
            'itemsDonated',
            'approvedWorks',
            'completedAppointmentsCount',
            'completedAppointmentsAsUpcyclerCount',
            'soldProducts',
            'donations',
            'isUpcycler'
        );

        $pdf = Pdf::loadView('profile.partials.dashboard-export', $data)->setPaper('a4', 'portrait');
        return $pdf->download('dashboard-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
