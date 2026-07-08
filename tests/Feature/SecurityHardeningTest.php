<?php

use App\Events\OrderPlacedNotification;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\StoreWorkRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Models\Categories;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('public registration cannot create an admin account', function () {
    $response = $this->post('/register', [
        'fname' => 'Ada',
        'lname' => 'Admin',
        'email' => 'ada@example.com',
        'role' => User::ROLE_ADMIN,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('role');
    expect(User::where('email', 'ada@example.com')->exists())->toBeFalse();
});

test('public request rules do not accept server owned moderation fields', function () {
    $productStoreRules = (new StoreProductRequest)->rules();
    expect(array_key_exists('approval_status', $productStoreRules))->toBeFalse();
    expect(array_key_exists('admin_notes', $productStoreRules))->toBeFalse();
    expect(array_key_exists('status', $productStoreRules))->toBeFalse();

    $donationStoreRules = (new StoreDonationRequest)->rules();
    expect(array_key_exists('approval_status', $donationStoreRules))->toBeFalse();
    expect(array_key_exists('admin_notes', $donationStoreRules))->toBeFalse();
    expect(array_key_exists('status', $donationStoreRules))->toBeFalse();

    expect(array_key_exists('admin_notes', (new UpdateDonationRequest)->rules()))->toBeFalse();

    expect(array_key_exists('approval_status', (new StoreWorkRequest)->rules()))->toBeFalse();
});

test('non seller cannot update an order status', function () {
    $seller = User::factory()->create(['role' => User::ROLE_USER]);
    $buyer = User::factory()->create(['role' => User::ROLE_USER]);
    $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
    $category = Categories::factory()->create();
    $product = Product::factory()->create([
        'user_id' => $seller->id,
        'category_id' => $category->id,
        'listingtype' => 'for sale',
        'status' => 'available',
        'approval_status' => 'approved',
    ]);
    $order = Order::create([
        'product_id' => $product->id,
        'buyer_id' => $buyer->id,
        'status' => 'pending',
    ]);

    $this->actingAs($otherUser, 'web')
        ->patch(route('orders.updateStatus', [$order, 'approved']))
        ->assertForbidden();
});

test('seller can update an order status', function () {
    Event::fake([OrderPlacedNotification::class]);

    $seller = User::factory()->create(['role' => User::ROLE_USER]);
    $buyer = User::factory()->create(['role' => User::ROLE_USER]);
    $category = Categories::factory()->create();
    $product = Product::factory()->create([
        'user_id' => $seller->id,
        'category_id' => $category->id,
        'listingtype' => 'for sale',
        'status' => 'available',
        'approval_status' => 'approved',
    ]);
    $order = Order::create([
        'product_id' => $product->id,
        'buyer_id' => $buyer->id,
        'status' => 'pending',
    ]);

    $this->actingAs($seller, 'web')
        ->patch(route('orders.updateStatus', [$order, 'approved']))
        ->assertRedirect();

    expect($order->fresh()->status)->toBe('approved');
    expect($product->fresh()->status)->toBe('sold');
});

test('image proxy rejects non s3 urls', function () {
    config(['filesystems.disks.s3.url' => 'https://bucket.test']);

    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $this->actingAs($user)
        ->get(route('proxy.image', ['url' => 'https://example.com/image.jpg']))
        ->assertForbidden();
});
