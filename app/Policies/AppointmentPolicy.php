<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin()
            || $user->id === $appointment->user_id
            || $user->id === $appointment->upcycler_id;
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin() || $user->id === $appointment->user_id;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin() || $user->id === $appointment->user_id;
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin() || $user->id === $appointment->user_id;
    }
}
