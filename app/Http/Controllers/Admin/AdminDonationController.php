<?php

namespace App\Http\Controllers\Admin;

use App\Events\DonationStatusNotification;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalStatusDonationUpdateRequest;
use App\Models\Donation;
use App\Models\Notification;
use App\Services\DonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDonationController extends Controller
{
    protected $donationService;

    public function __construct(DonationService $donationService)
    {
        $this->donationService = $donationService;
    }

    public function index(): View
    {
        // 1. APPROVAL TABLES (Unique pagination names)
        $approvedDonations = $this->donationService->getDonationsByStatusPaginated(
            'approved', 10, 'approved_page'
        );

        $pendingDonations = $this->donationService->getDonationsByStatusPaginated(
            'pending', 10, 'pending_page'
        );

        $rejectedDonations = $this->donationService->getDonationsByStatusPaginated(
            'rejected', 10, 'rejected_page'
        );

        // 2. REWARD TABLES (Unique pagination names + Service filters 'pending' for proofs)
        $pendingVerifications = $this->donationService->getDonationsByVerificationStatusPaginated(
            'pending', 10, 'reward_pending_page'
        );

        $verifiedDonations = $this->donationService->getDonationsByVerificationStatusPaginated(
            'approved', 10, 'reward_verified_page'
        );

        $rejectedProofs = $this->donationService->getDonationsByVerificationStatusPaginated(
            'rejected', 10, 'reward_rejected_page'
        );

        return view('admin.donations.index', compact(
            'approvedDonations',
            'pendingDonations',
            'rejectedDonations',
            'pendingVerifications',
            'verifiedDonations',
            'rejectedProofs'
        ));
    }

    public function show(Donation $donation): View
    {
        $donation->load(['user', 'category', 'comments.user']);

        return view('admin.donations.show', compact('donation'));
    }

    public function update(ApprovalStatusDonationUpdateRequest $request, Donation $donation): RedirectResponse
    {
        $validated = $request->validated();
        $oldStatus = $donation->approval_status;

        $donation->update($validated);
        $donation->refresh();

        // Send notification if approval status changed
        if (isset($validated['approval_status']) && $validated['approval_status'] !== $oldStatus) {
            Notification::create([
                'user_id' => $donation->user_id,
                'type' => 'donation_status',
                'data' => [
                    'status' => $validated['approval_status'],
                    'donation_id' => $donation->id,
                    'message' => $validated['approval_status'] === 'approved'
                        ? 'Your donation has been approved!'
                        : 'Your donation has been rejected.',
                    'from_user' => 'System',
                    'profile_pic_url' => null, // Triggers System Logo fallback
                ],
            ]);

            broadcast(new DonationStatusNotification($donation, $donation->user_id, $validated['approval_status']))->toOthers();
        }

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation approval status updated successfully.');
    }

    public function edit(Donation $donation): View
    {
        $donation->load(['user', 'category']);

        return view('admin.donation.edit', compact('donation'));
    }

    public function destroy(Donation $donation): RedirectResponse
    {
        $this->donationService->deleteDonation($donation);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation deleted successfully.');
    }

    public function approve(Donation $donation): RedirectResponse
    {
        $this->donationService->updateDonation($donation, ['approval_status' => 'approved']);
        $donation->refresh();

        Notification::create([
            'user_id' => $donation->user_id,
            'type' => 'donation_status',
            'data' => [
                'status' => 'approved',
                'donation_id' => $donation->id,
                'message' => 'Your donation has been approved!',
                'from_user' => 'System',
                'profile_pic_url' => null,
            ],
        ]);

        broadcast(new DonationStatusNotification($donation, $donation->user_id, 'approved'))->toOthers();

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation approved successfully.');
    }

    public function reject(Donation $donation): RedirectResponse
    {
        $this->donationService->updateDonation($donation, ['approval_status' => 'rejected']);
        $donation->refresh();

        Notification::create([
            'user_id' => $donation->user_id,
            'type' => 'donation_status',
            'data' => [
                'status' => 'rejected',
                'donation_id' => $donation->id,
                'message' => 'Your donation has been rejected.',
                'from_user' => 'System',
                'profile_pic_url' => null,
            ],
        ]);

        broadcast(new DonationStatusNotification($donation, $donation->user_id, 'rejected'))->toOthers();

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation rejected successfully.');
    }

    public function verifyDonation(Donation $donation): RedirectResponse
    {
        // This method can be kept if needed for direct verification without points, otherwise verifyProof handles the main logic
        $this->donationService->updateDonation($donation, ['verification_status' => 'approved']);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation verified successfully.');
    }

    public function rejectDonationProof(Request $request, Donation $donation): RedirectResponse
    {
        $admin_notes = $request->input('admin_notes');

        $this->donationService->updateDonation($donation, [
            'verification_status' => 'rejected',
            'admin_notes' => $admin_notes,
        ]);

        // Send Rejection Notification
        Notification::create([
            'user_id' => $donation->user_id,
            'type' => 'donation_status',
            'data' => [
                'status' => 'rejected',
                'donation_id' => $donation->id,
                'message' => 'Your donation proof was rejected. Reason: '.$admin_notes,
                'from_user' => 'System',
                'profile_pic_url' => null,
            ],
        ]);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation proof rejected successfully.');
    }

    public function verifyProof(Donation $donation)
    {
        // Make sure the donation has a proof and is pending verification
        if ($donation->verification_status !== 'pending') {
            return back()->with('error', 'This donation is not pending verification.');
        }

        // Update verification status and award points
        $donation->update([
            'verification_status' => 'approved',
            'status' => 'donated',
        ]);

        // Add 20 points to the donor’s account
        $donation->user->increment('points', 20);

        // Send Success Notification
        Notification::create([
            'user_id' => $donation->user_id,
            'type' => 'donation_status',
            'data' => [
                'status' => 'verified',
                'donation_id' => $donation->id,
                'message' => 'Your donation proof has been verified! You received 20 points.',
                'from_user' => 'System',
                'profile_pic_url' => null,
            ],
        ]);

        return back()->with('success', 'Donation verified and 20 points awarded successfully!');
    }
}
