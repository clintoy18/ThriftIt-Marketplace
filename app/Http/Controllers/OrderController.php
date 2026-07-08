<?php

namespace App\Http\Controllers;

use App\Events\OrderPlacedNotification;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Services\FileStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        private readonly FileStorageService $files
    ) {}

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // 1. PREVENT SELF-PURCHASE
        if ($product->user_id === Auth::id()) {
            return redirect()
                ->route('products.show', $product->id)
                ->with('error', 'You cannot buy your own product.');
        }

        // Check if this user already placed an order for this product
        $existingOrder = Order::where('product_id', $product->id)
            ->where('buyer_id', Auth::id())
            ->first();

        if ($existingOrder) {
            return redirect()
                ->route('products.show', $product->id)
                ->with('error', 'You already placed an order for this product.');
        }

        $path = $this->files->uploadPublic($request->file('proof'), 'proofs');

        // Create order
        $order = Order::create([
            'product_id' => $product->id,
            'buyer_id' => Auth::id(),
            'proof' => $path,
        ]);

        // Save notification in DB for the seller
        Notification::create([
            'user_id' => $product->user_id, // seller
            'type' => 'order_notification',
            'data' => [
                'order_id' => $order->id,
                'buyer_name' => Auth::user()->fname.' '.Auth::user()->lname,
                // FIXED: Added space after "from"
                'message' => 'You received a new order from '.Auth::user()->fname.' '.Auth::user()->lname,
            ],
        ]);

        // Broadcast notification in real time
        event(new OrderPlacedNotification($order, $product->user_id));

        return redirect()
            ->route('products.show', $product->id)
            ->with('success', 'Proof of payment uploaded successfully. Please wait for seller confirmation.');
    }

    public function updateStatus($order, string $status)
    {
        if (! $order instanceof Order) {
            $order = Order::findOrFail($order);
        }

        $this->authorize('updateStatus', $order);

        $allowedStatuses = ['pending', 'approved', 'delivering', 'completed', 'cancelled'];

        if (! in_array($status, $allowedStatuses)) {
            return back()->with('error', 'Invalid status.');
        }

        DB::transaction(function () use ($order, $status) {
            $order->loadMissing('product');

            if ($status === 'approved' && $order->product) {
                $order->product->update(['status' => 'sold']);
            }

            if ($status === 'cancelled' && $order->product) {
                $order->product->update(['status' => 'available']);
            }

            $order->update(['status' => $status]);
        });

        $order->refresh()->loadMissing('product');

        // Prepare notification message based on status
        $message = match ($status) {
            'approved' => "Your order for {$order->product->name} has been approved by the seller.",
            'delivering' => "Your order for {$order->product->name} is now out for delivery.",
            'completed' => "Your order for {$order->product->name} has been completed. Enjoy your item!",
            'cancelled' => "Your order for {$order->product->name} has been cancelled.",
            default => "The status of your order for {$order->product->name} has been updated to ".ucfirst($status).'.',
        };

        // Create notification for buyer
        Notification::create([
            'user_id' => $order->buyer_id,
            'type' => 'order_status_update',
            'data' => [
                'order_id' => $order->id,
                'product_name' => $order->product->name ?? 'product',
                'status' => $status,
                'message' => $message,
            ],
        ]);

        // Broadcast notification in real-time
        event(new OrderPlacedNotification($order, $order->buyer_id));

        return back()->with('success', 'Order status updated to '.ucfirst($status));
    }
}
