<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Appointment;

class Subscribed
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'You need to log in first.');
        }

        // --- 1. SET LIMITS ---
        $listingLimit = 5;
        $appointmentLimit = 5; // Free users get 5 completed appointments

        if ($user->subscribedToProduct('prod_TXeLIH6C3eBbuY')) {
            $listingLimit = 10;
            $appointmentLimit = 10;
        } elseif ($user->subscribedToProduct('prod_TXeK42zx4mikcP')) {
            $listingLimit = 40;
            $appointmentLimit = 20;
        } elseif ($user->subscribedToProduct('prod_TXeIKafMmVOk7B')) {
            $listingLimit = null;
            $appointmentLimit = null;
        }

        // --- 2. CHECK PRODUCT LIMIT (When creating products) ---
        // Make sure your routes for products are named 'listings.*' or 'products.*'
        if ($request->routeIs('products.create', 'products.store')) {
            $listingCount = $user->products()->count();
            if ($listingLimit !== null && $listingCount >= $listingLimit) {
                return redirect()->route('pricing.index')
                    ->with('error', 'You reached your product limit. Upgrade to add more.');
            }
        }

        // --- 3. CHECK APPOINTMENT LIMIT (When approving appointments) ---
        // We check if the route is 'upcycler.update' AND if the status is 'approved'
        if ($request->routeIs('upcycler.update')) {

            // Check if they are trying to Approve a new one
            // (Make sure to use 'appstatus' if that is your form input name)
            if ($request->input('appstatus') === 'approved') {

                // COUNT BOTH APPROVED AND COMPLETED
                // FIX: Count where this user is the UPCYCLER, not the customer
                $totalWorkCount = Appointment::where('upcycler_id', $user->id)
                    ->whereIn('appstatus', ['approved', 'completed'])
                    ->count();

                if ($appointmentLimit !== null && $totalWorkCount >= $appointmentLimit) {
                    return redirect()->route('pricing.index')
                        ->with('error', 'You have reached your limit of active and completed appointments. Upgrade to accept more.');
                }
            }
        }

        return $next($request);
    }
}
