<?php

namespace App\Policies;

use App\Models\User;

class MessagePolicy
{
    public function message(User $user, User $recipient): bool
    {
        return $user->id !== $recipient->id
            && $recipient->is_active
            && ! $user->hasBlocked($recipient->id)
            && ! $recipient->hasBlocked($user->id);
    }
}
