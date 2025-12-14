<?php

namespace App\Repositories;

use App\Models\Donation;
use App\Models\Segment;

class DonationRepository
{
    public function all()
    {
        return Donation::where('approval_status', 'approved')
            ->where('status', 'available')
            ->with('donationImages')
            ->get();
    }

    public function find($id)
    {
        return Donation::with('donationImages')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Donation::create($data);
    }

    public function update(Donation $donation, array $data)
    {
        $donation->update($data);
        return $donation;
    }

    public function delete(Donation $donation)
    {
        return $donation->delete();
    }

    public function getByUser($userId)
    {
        return Donation::where('user_id', $userId)
            ->with('donationImages')
            ->latest()
            ->get();
    }

    public function getMoreByUser($userId, $excludeDonationId, $limit = 6)
    {
        return Donation::where('user_id', $userId)
            ->where('id', '!=', $excludeDonationId)
            ->with('donationImages')
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getApprovedDonationsBySegement(Segment $segment, ?int $categoryId = null)
    {
        $query = $segment->donations()
            ->where('approval_status', 'approved')
            ->where('status', 'available')
            ->with(['category', 'donationImages']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->get();
    }

    // --- UPDATED METHODS BELOW ---

    /**
     * Now accepts $pageName to prevent pagination conflicts.
     */
    public function getByStatusPaginated(string $status, int $perPage = 10, string $pageName = 'page')
    {
        return Donation::with(['user', 'category'])
            ->where('approval_status', $status)
            ->latest()
            // 3rd argument is the custom page parameter name (e.g. 'approved_page')
            ->paginate($perPage, ['*'], $pageName);
    }

    /**
     * Now accepts $pageName AND filters out pending items with no proof.
     */
    public function getByVerificationStatusPaginated(string $status, int $perPage = 10, string $pageName = 'page')
    {
        $query = Donation::with(['user', 'category'])
            ->where('verification_status', $status);

        // FIX: If status is 'pending', ONLY show items that actually have a proof.
        if ($status === 'pending') {
            $query->whereNotNull('proof')->where('proof', '!=', '');
        }

        return $query->orderBy('updated_at', 'desc')
            ->paginate($perPage, ['*'], $pageName);
    }
}