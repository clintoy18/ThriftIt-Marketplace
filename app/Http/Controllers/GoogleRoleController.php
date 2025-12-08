<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleRoleController extends Controller
{
    public function showRoleSelection()
    {
        // Only allow logged-in users without a role
        $user = Auth::user();
        if ($user->role !== null) {
            return redirect()->route('dashboard');
        }
        return view('auth.google-role'); // create this Blade
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'role' => 'required|in:0,1',
        ]);

        $user = Auth::user();
        $user->role = $request->role;
        $user->save();

        // Redirect based on role
        if ($user->role == 1) {
            return redirect()->route('upcycler')->with('success', 'Role assigned successfully!');
        }

        return redirect()->route('dashboard')->with('success', 'Role assigned successfully!');
    }
}
