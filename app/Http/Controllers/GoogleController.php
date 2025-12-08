<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'fname' => $googleUser->user['given_name'] ?? '',
                'lname' => $googleUser->user['family_name'] ?? '',
                'password' => bcrypt('google_' . $googleUser->getId()),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now()
            ]
        );

        Auth::login($user);

        // Redirect to role selection if no role
        if ($user->role === null) {
            return redirect()->route('google.role.select');
        }

        // Otherwise, normal dashboard redirect
        return redirect('/dashboard');
    }
}
