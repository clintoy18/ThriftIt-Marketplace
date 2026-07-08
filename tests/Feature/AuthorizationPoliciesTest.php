<?php

use App\Models\Appointment;
use App\Models\BlockedUser;
use App\Models\Categories;
use App\Models\Donation;
use App\Models\Message;
use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use App\Models\Work;
use Illuminate\Support\Facades\Gate;

test('product and donation policies enforce ownership', function () {
    $owner = User::factory()->create(['role' => User::ROLE_USER]);
    $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $category = Categories::factory()->create();

    $product = Product::factory()->create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'listingtype' => 'for sale',
        'status' => 'available',
        'approval_status' => 'approved',
    ]);

    $donation = Donation::create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'name' => 'Reusable jacket',
        'description' => 'A clean jacket ready for donation.',
        'status' => 'available',
        'approval_status' => 'approved',
    ]);

    expect(Gate::forUser($owner)->allows('update', $product))->toBeTrue();
    expect(Gate::forUser($owner)->allows('markAsSold', $product))->toBeTrue();
    expect(Gate::forUser($otherUser)->denies('update', $product))->toBeTrue();
    expect(Gate::forUser($otherUser)->denies('delete', $product))->toBeTrue();
    expect(Gate::forUser($admin)->allows('delete', $product))->toBeTrue();

    expect(Gate::forUser($owner)->allows('update', $donation))->toBeTrue();
    expect(Gate::forUser($owner)->allows('markAsDonated', $donation))->toBeTrue();
    expect(Gate::forUser($otherUser)->denies('update', $donation))->toBeTrue();
    expect(Gate::forUser($otherUser)->denies('delete', $donation))->toBeTrue();
    expect(Gate::forUser($admin)->allows('delete', $donation))->toBeTrue();
});

test('users cannot access another users appointment routes', function () {
    $requester = User::factory()->create(['role' => User::ROLE_USER]);
    $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
    $upcycler = User::factory()->create(['role' => User::ROLE_UPCYCLER]);

    $appointment = Appointment::create([
        'user_id' => $requester->id,
        'upcycler_id' => $upcycler->id,
        'appdetails' => 'Resize this denim jacket for daily use.',
        'contactnumber' => '09171234567',
        'apptype' => 'Resize',
        'appstatus' => 'pending',
        'appdate' => now()->addDays(5),
        'app_time' => '10:00',
    ]);

    $this->actingAs($otherUser, 'web')
        ->get(route('appointments.show', $appointment->appointmentid))
        ->assertForbidden();

    $this->actingAs($otherUser, 'web')
        ->get(route('appointments.edit', $appointment->appointmentid))
        ->assertForbidden();

    $this->actingAs($otherUser, 'web')
        ->patch(route('appointments.update', $appointment->appointmentid), [
            'appstatus' => 'pending',
            'appdetails' => 'Resize this denim jacket for daily use.',
            'contactnumber' => '09171234567',
        ])
        ->assertForbidden();

    $this->actingAs($otherUser, 'web')
        ->patch(route('appointments.cancel', $appointment->appointmentid))
        ->assertForbidden();

    $this->actingAs($otherUser, 'web')
        ->delete(route('appointments.destroy', $appointment->appointmentid))
        ->assertForbidden();

    expect($appointment->fresh())->not->toBeNull();
    expect($appointment->fresh()->appstatus)->toBe('pending');
});

test('appointment policy allows only participants and admins', function () {
    $requester = User::factory()->create(['role' => User::ROLE_USER]);
    $upcycler = User::factory()->create(['role' => User::ROLE_UPCYCLER]);
    $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $appointment = Appointment::create([
        'user_id' => $requester->id,
        'upcycler_id' => $upcycler->id,
        'appdetails' => 'Patch this tote bag with new fabric.',
        'contactnumber' => '09171234567',
        'apptype' => 'Patchwork',
        'appstatus' => 'pending',
        'appdate' => now()->addDays(5),
        'app_time' => '11:00',
    ]);

    expect(Gate::forUser($requester)->allows('view', $appointment))->toBeTrue();
    expect(Gate::forUser($upcycler)->allows('view', $appointment))->toBeTrue();
    expect(Gate::forUser($admin)->allows('update', $appointment))->toBeTrue();
    expect(Gate::forUser($otherUser)->denies('view', $appointment))->toBeTrue();
    expect(Gate::forUser($upcycler)->denies('update', $appointment))->toBeTrue();
});

test('report policy limits updates to admins', function () {
    $reporter = User::factory()->create(['role' => User::ROLE_USER]);
    $reportedUser = User::factory()->create(['role' => User::ROLE_USER]);
    $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $report = Report::create([
        'reporter_id' => $reporter->id,
        'reported_user_id' => $reportedUser->id,
        'reason' => 'spam',
        'description' => 'Repeated spam messages.',
        'status' => 'pending',
    ]);

    expect(Gate::forUser($reporter)->allows('view', $report))->toBeTrue();
    expect(Gate::forUser($reporter)->denies('update', $report))->toBeTrue();
    expect(Gate::forUser($reporter)->allows('delete', $report))->toBeTrue();
    expect(Gate::forUser($otherUser)->denies('view', $report))->toBeTrue();
    expect(Gate::forUser($admin)->allows('update', $report))->toBeTrue();
});

test('message policy blocks self inactive and blocked recipients', function () {
    $sender = User::factory()->create(['role' => User::ROLE_USER]);
    $recipient = User::factory()->create(['role' => User::ROLE_USER]);
    $inactiveRecipient = User::factory()->create([
        'role' => User::ROLE_USER,
        'is_active' => false,
    ]);

    expect(Gate::forUser($sender)->allows('message', [Message::class, $recipient]))->toBeTrue();
    expect(Gate::forUser($sender)->denies('message', [Message::class, $sender]))->toBeTrue();
    expect(Gate::forUser($sender)->denies('message', [Message::class, $inactiveRecipient]))->toBeTrue();

    BlockedUser::create([
        'user_id' => $sender->id,
        'blocked_user_id' => $recipient->id,
    ]);

    expect(Gate::forUser($sender)->denies('message', [Message::class, $recipient]))->toBeTrue();
});

test('profile and work policies enforce owner scoped changes', function () {
    $owner = User::factory()->create(['role' => User::ROLE_UPCYCLER]);
    $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $work = Work::create([
        'user_id' => $owner->id,
        'title' => 'Denim repair',
        'description' => 'Before and after upcycling work.',
        'upcycle_type' => 'Patchwork',
        'approval_status' => 'pending',
    ]);

    expect(Gate::forUser($owner)->allows('update', $owner))->toBeTrue();
    expect(Gate::forUser($otherUser)->denies('update', $owner))->toBeTrue();
    expect(Gate::forUser($admin)->allows('delete', $owner))->toBeTrue();

    expect(Gate::forUser($owner)->allows('view', $work))->toBeTrue();
    expect(Gate::forUser($owner)->allows('update', $work))->toBeTrue();
    expect(Gate::forUser($otherUser)->denies('view', $work))->toBeTrue();
    expect(Gate::forUser($otherUser)->denies('update', $work))->toBeTrue();
    expect(Gate::forUser($admin)->allows('delete', $work))->toBeTrue();
});

test('non owners cannot mark products as sold through the route', function () {
    $owner = User::factory()->create(['role' => User::ROLE_USER]);
    $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
    $category = Categories::factory()->create();
    $product = Product::factory()->create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'listingtype' => 'for sale',
        'status' => 'available',
        'approval_status' => 'approved',
    ]);

    $this->actingAs($otherUser, 'web')
        ->put(route('products.markAsSold', $product))
        ->assertForbidden();

    expect($product->fresh()->status)->toBe('available');
});
