<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\Order;
use App\Policies\OrderPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);

        // Keep the global navigation badge cheap; conversation lists are loaded by message pages only.
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('totalUnreadCount', Message::where('receiver_id', Auth::id())
                    ->where('is_read', false)
                    ->count());

                return;
            }

            $view->with('totalUnreadCount', 0);
        });
    }
}
