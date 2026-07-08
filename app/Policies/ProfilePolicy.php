<?php

namespace App\Policies;

use App\Models\User;

class ProfilePolicy
{
    public function view(User $user, User $profile): bool
    {
        return true;
    }

    public function update(User $user, User $profile): bool
    {
        return $user->isAdmin() || $user->id === $profile->id;
    }

    public function delete(User $user, User $profile): bool
    {
        return $user->isAdmin() || $user->id === $profile->id;
    }

    public function uploadVerificationDocument(User $user, User $profile): bool
    {
        return $user->id === $profile->id;
    }

    public function exportDashboard(User $user, User $profile): bool
    {
        return $user->isAdmin() || $user->id === $profile->id;
    }
}
