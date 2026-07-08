<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class OrderPolicy
{
    public function updateStatus(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return Product::whereKey($order->product_id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
