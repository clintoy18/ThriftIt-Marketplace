<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    public function update(User $user, Donation $donation): bool
    {
        return $user->isAdmin() || $user->id === $donation->user_id;
    }

    public function delete(User $user, Donation $donation): bool
    {
        return $user->isAdmin() || $user->id === $donation->user_id;
    }

    public function markAsDonated(User $user, Donation $donation): bool
    {
        return $user->isAdmin() || $user->id === $donation->user_id;
    }
}
