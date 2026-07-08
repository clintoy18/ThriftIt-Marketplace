<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Work;

class WorkPolicy
{
    public function view(User $user, Work $work): bool
    {
        return $work->approval_status === 'approved'
            || $user->isAdmin()
            || $user->id === $work->user_id;
    }

    public function update(User $user, Work $work): bool
    {
        return $user->isAdmin() || $user->id === $work->user_id;
    }

    public function delete(User $user, Work $work): bool
    {
        return $user->isAdmin() || $user->id === $work->user_id;
    }
}
