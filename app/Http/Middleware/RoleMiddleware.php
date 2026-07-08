<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $authUserRole = Auth::user()->role;

        switch ($role) {
            case 'admin':
                if ($authUserRole === User::ROLE_ADMIN) {
                    return $next($request);
                }
                break;
            case 'upcycler':
                if ($authUserRole === User::ROLE_UPCYCLER) {
                    return $next($request);
                }
                break;
            case 'user':
                if ($authUserRole === User::ROLE_USER) {
                    return $next($request);
                }
                break;

        }

        switch ($authUserRole) {
            case User::ROLE_ADMIN:
                return redirect()->route('admin.dashboard');
            case User::ROLE_UPCYCLER:
                return redirect()->route('upcycler');

            case User::ROLE_USER:
                return redirect()->route('dashboard');

        }

        return redirect()->route('login');
    }
}
