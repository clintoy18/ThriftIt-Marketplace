<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalStatusDonationUpdateRequest;
use App\Models\Donation;
use App\Models\Notification;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\DonationService;
use App\Events\DonationStatusNotification;

class AdminDonationController extends Controller
{

    protected $donationService;
    public function __construct(DonationService $donationService)
    {
        $this->donationService = $donationService;
    }
    public function index(): View
    {
        $approvedDonations = $this->donationService->getDonationsByStatusPaginated('approved');
        $pendingDonations = $this->donationService->getDonationsByStatusPaginated('pending');
        $rejectedDonations = $this->donationService->getDonationsByStatusPaginated('rejected');
        //reward donate management
        $pendingVerifications = Donation::where('verification_status', 'pending')->get();
        $verifiedDonations = Donation::where('verification_status', 'approved')->get();
        $rejectedProofs = Donation::where('verification_status', 'rejected')->get();

        return view('admin.donations.index', compact('approvedDonations', 'pendingDonations','rejectedDonations','pendingVerifications',
            'verifiedDonations',
            'rejectedProofs'));
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
        $donation->refresh(); // Refresh to get latest data

        // Send notification if approval status changed
        if (isset($validated['approval_status']) && $validated['approval_status'] !== $oldStatus) {
            // Save notification in DB
            Notification::create([
                'user_id' => $donation->user_id,
                'type' => 'donation_status',
                'data' => [
                    'status' => $validated['approval_status'],
                    'donation_id' => $donation->id,
                    'message' => $validated['approval_status'] === 'approved' 
                        ? 'Your donation has been approved!' 
                        : 'Your donation has been rejected.'
                ],
            ]);

            // Broadcast real-time notification
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
        $donation->delete();

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation deleted successfully.');
    }

    public function approve(Donation $donation): RedirectResponse
    {
        $this->donationService->updateDonation($donation, ['approval_status' => 'approved']);
        $donation->refresh(); // Refresh to get latest data

        // Save notification in DB
        Notification::create([
            'user_id' => $donation->user_id,
            'type' => 'donation_status',
            'data' => [
                'status' => 'approved',
                'donation_id' => $donation->id,
                'message' => 'Your donation has been approved!'
            ],
        ]);

        // Broadcast real-time notification
        broadcast(new DonationStatusNotification($donation, $donation->user_id, 'approved'))->toOthers();

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation approved successfully.');
    }

    public function reject(Donation $donation): RedirectResponse
    {
        $this->donationService->updateDonation($donation, ['approval_status' => 'rejected']);
        $donation->refresh(); // Refresh to get latest data

        // Save notification in DB
        Notification::create([
            'user_id' => $donation->user_id,
            'type' => 'donation_status',
            'data' => [
                'status' => 'rejected',
                'donation_id' => $donation->id,
                'message' => 'Your donation has been rejected.'
            ],
        ]);

        // Broadcast real-time notification
        broadcast(new DonationStatusNotification($donation, $donation->user_id, 'rejected'))->toOthers();

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation rejected successfully.');
    }

    public function verifyDonation(Donation $donation): RedirectResponse
    {
        $this->donationService->updateDonation($donation, ['verification_status' => 'approved']);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation verified successfully. Points added.');
    }
    public function rejectDonationProof(Donation $donation): RedirectResponse
    {
        $this->donationService->updateDonation($donation, ['verification_status' => 'rejected']);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation rejected successfully.');
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

        return back()->with('success', 'Donation verified and 20 points awarded successfully!');
    }
}
