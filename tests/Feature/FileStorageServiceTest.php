<?php

use App\Models\Appointment;
use App\Models\Categories;
use App\Models\Donation;
use App\Models\Product;
use App\Models\User;
use App\Models\Work;
use App\Services\AppointmentService;
use App\Services\DonationService;
use App\Services\FileStorageService;
use App\Services\ProductService;
use App\Services\WorkService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('file storage service uploads replaces moves and deletes public files', function () {
    Storage::fake('s3');

    $files = app(FileStorageService::class);

    $uploadedPath = $files->uploadPublic(UploadedFile::fake()->create('avatar.jpg'), 'profiles');
    expect($uploadedPath)->toStartWith('profiles/');
    Storage::disk('s3')->assertExists($uploadedPath);

    Storage::disk('s3')->put('profiles/old.jpg', 'old');
    $replacementPath = $files->replacePublic('profiles/old.jpg', UploadedFile::fake()->create('new.jpg'), 'profiles');
    Storage::disk('s3')->assertMissing('profiles/old.jpg');
    Storage::disk('s3')->assertExists($replacementPath);

    Storage::disk('s3')->put('temp/file.jpg', 'temporary');
    expect($files->movePublicIfExists('temp/file.jpg', 'final/file.jpg'))->toBeTrue();
    Storage::disk('s3')->assertMissing('temp/file.jpg');
    Storage::disk('s3')->assertExists('final/file.jpg');

    $files->deleteManyIfExists([$uploadedPath, $replacementPath, 'missing/file.jpg']);
    Storage::disk('s3')->assertMissing($uploadedPath);
    Storage::disk('s3')->assertMissing($replacementPath);
});

test('product delete removes related s3 files', function () {
    Storage::fake('s3');

    $owner = User::factory()->create(['role' => User::ROLE_USER]);
    $category = Categories::factory()->create();
    $paths = [
        'products/main.jpg',
        'qr_codes/payment.jpg',
        'products/gallery.jpg',
    ];

    foreach ($paths as $path) {
        Storage::disk('s3')->put($path, 'file');
    }

    $product = Product::factory()->create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'listingtype' => 'for sale',
        'status' => 'available',
        'approval_status' => 'approved',
        'image' => $paths[0],
        'qr_code' => $paths[1],
    ]);
    $product->images()->create(['image' => $paths[2]]);

    app(ProductService::class)->deleteProduct($product);

    foreach ($paths as $path) {
        Storage::disk('s3')->assertMissing($path);
    }

    expect(Product::find($product->id))->toBeNull();
});

test('donation delete removes related s3 files', function () {
    Storage::fake('s3');

    $owner = User::factory()->create(['role' => User::ROLE_USER]);
    $category = Categories::factory()->create();
    $paths = [
        'donation_images/main.jpg',
        'donation_proofs/proof.jpg',
        'donations_images/gallery.jpg',
    ];

    foreach ($paths as $path) {
        Storage::disk('s3')->put($path, 'file');
    }

    $donation = Donation::create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'name' => 'Reusable coat',
        'description' => 'A clean coat ready for donation.',
        'image' => $paths[0],
        'proof' => $paths[1],
        'status' => 'available',
        'approval_status' => 'approved',
    ]);
    $donation->donationImages()->create(['image' => $paths[2]]);

    app(DonationService::class)->deleteDonation($donation);

    foreach ($paths as $path) {
        Storage::disk('s3')->assertMissing($path);
    }

    expect(Donation::find($donation->id))->toBeNull();
});

test('appointment and work deletes remove related s3 files', function () {
    Storage::fake('s3');

    $requester = User::factory()->create(['role' => User::ROLE_USER]);
    $upcycler = User::factory()->create(['role' => User::ROLE_UPCYCLER]);
    $paths = [
        'appointment_images/request.jpg',
        'works_images/example.jpg',
    ];

    foreach ($paths as $path) {
        Storage::disk('s3')->put($path, 'file');
    }

    $appointment = Appointment::create([
        'user_id' => $requester->id,
        'upcycler_id' => $upcycler->id,
        'appdetails' => 'Resize a denim jacket for daily use.',
        'contactnumber' => '09171234567',
        'apptype' => 'Resize',
        'appstatus' => 'pending',
        'appdate' => now()->addDays(5),
        'app_time' => '10:00',
    ]);
    $appointment->apptImages()->create(['image_path' => $paths[0]]);

    $work = Work::create([
        'user_id' => $upcycler->id,
        'title' => 'Denim repair',
        'description' => 'Before and after upcycling work.',
        'upcycle_type' => 'Patchwork',
        'approval_status' => 'pending',
    ]);
    $work->images()->create(['image' => $paths[1]]);

    app(AppointmentService::class)->deleteAppointment($appointment);
    app(WorkService::class)->deleteWork($work);

    foreach ($paths as $path) {
        Storage::disk('s3')->assertMissing($path);
    }

    expect(Appointment::find($appointment->appointmentid))->toBeNull();
    expect(Work::find($work->id))->toBeNull();
});
