<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Donation;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use App\Models\Work;
use App\Policies\AppointmentPolicy;
use App\Policies\DonationPolicy;
use App\Policies\MessagePolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProfilePolicy;
use App\Policies\ReportPolicy;
use App\Policies\WorkPolicy;
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
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Donation::class, DonationPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);
        Gate::policy(User::class, ProfilePolicy::class);
        Gate::policy(Work::class, WorkPolicy::class);

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
