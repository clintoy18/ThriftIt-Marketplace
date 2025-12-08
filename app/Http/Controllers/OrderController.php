<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\Notification;
use App\Events\OrderPlacedNotification;
use Illuminate\Support\Facades\Storage;


class OrderController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Check if this user already placed an order for this product
        $existingOrder = Order::where('product_id', $product->id)
            ->where('buyer_id', Auth::id())
            ->first();

        if ($existingOrder) {
            return redirect()
                ->route('products.show', $product->id)
                ->with('error', 'You already placed an order for this product.');
        }

        // Store proof image on S3
        $path = $request->file('proof')->store('proofs', 's3');

        // Make it publicly accessible
        Storage::disk('s3')->setVisibility($path, 'public');

        // Create order
        $order = Order::create([
            'product_id' => $product->id,
            'buyer_id'   => Auth::id(),
            'proof'      => $path,
        ]);

        // Save notification in DB for the seller
        Notification::create([
            'user_id' => $product->user_id, // seller
            'type'    => 'order_notification',
            'data'    => [
                'order_id'   => $order->id,
                'buyer_name' => Auth::user()->fname . ' ' . Auth::user()->lname,
                'message'    => "You received a new order from <b>" . Auth::user()->fname . ' ' . Auth::user()->lname . "</b>.",
            ],
        ]);

        // Broadcast notification in real time
        event(new OrderPlacedNotification($order, $product->user_id));

        return redirect()
            ->route('products.show', $product->id)
            ->with('success', 'Proof of payment uploaded successfully. Please wait for seller confirmation.');
    }

    public function updateStatus(Order $order, string $status)
    {
        $allowedStatuses = ['pending', 'approved', 'delivering', 'completed', 'cancelled'];

        if (!in_array($status, $allowedStatuses)) {
            return back()->with('error', 'Invalid status.');
        }

        // Update product status if order is approved or cancelled
        if ($status === 'approved' && $order->product) {
            $order->product->update(['status' => 'sold']);
        }

        if ($status === 'cancelled' && $order->product) {
            $order->product->update(['status' => 'available']);
        }

        // Update order status
        $order->update(['status' => $status]);

        // Prepare notification message based on status
        $message = match ($status) {
            'approved'   => "Your order for <b>{$order->product->name}</b> has been approved by the seller.",
            'delivering' => "Your order for <b>{$order->product->name}</b> is now out for delivery.",
            'completed'  => "Your order for <b>{$order->product->name}</b> has been completed. Enjoy your item!",
            'cancelled'  => "Your order for <b>{$order->product->name}</b> has been cancelled.",
            default      => "The status of your order for <b>{$order->product->name}</b> has been updated to <b>" . ucfirst($status) . "</b>.",
        };

        // Create notification for buyer
        Notification::create([
            'user_id' => $order->buyer_id,
            'type'    => 'order_status_update',
            'data'    => [
                'order_id'     => $order->id,
                'product_name' => $order->product->name ?? 'product',
                'status'       => $status,
                'message'      => $message,
            ],
        ]);

        // Broadcast notification in real-time
        event(new \App\Events\OrderPlacedNotification($order, $order->buyer_id));

        return back()->with('success', 'Order status updated to ' . ucfirst($status));
    }
}
