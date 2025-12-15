<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Product;
use App\Models\Barangay;
use App\Models\Donation;
use App\Models\Appointment;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Cashier\Billable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'role',
        'password',
        'is_active',
        'points',
        'is_verified',
        'verification_status',
        'verification_document',
        'verification_document_back',
        'profile_pic',
        'barangay_id',
        'suspended_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'integer',
            'is_active' => 'boolean',
            'suspended_until' => 'datetime', // 
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 2;
    }

    /**
     * Check if user is an upcycler
     */
    public function isUpcycler(): bool
    {
        return $this->role === 1;
    }

    /**
     * Check if user is a regular user
     */
    public function isRegularUser(): bool
    {
        return $this->role === 0;
    }

    /**
     * Get role name
     */
    public function getRoleNameAttribute(): string
    {
        return match ($this->role) {
            2 => 'Admin',
            1 => 'Upcycler',
            0 => 'User',
            default => 'Unknown'
        };
    }

    public function reportsReceived()
    {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    // 2. Accessor: Calculate "Strikes" live
    // Usage: $user->strikes
    public function getStrikesAttribute()
    {
        return $this->reportsReceived()
            ->where('status', 'resolved')
            ->count();
    }

    public function reviewsWritten()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }

    /**
     * Get the average rating from reviews received
     */
    public function getAverageRatingAttribute(): float
    {
        $average = $this->reviewsReceived()->avg('rating');
        return $average ? round($average, 1) : 0.0;
    }

    /**
     * Get the total count of reviews received
     */
    public function getReviewCountAttribute(): int
    {
        return $this->reviewsReceived()->count();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

    public function ecoPosts()
    {
        return $this->hasMany(EcoEducationalPost::class, 'user_id');
    }


    // Orders placed by this user (as a buyer)
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    // Orders received on products this user is selling
    public function ordersAsSeller()
    {
        return $this->hasManyThrough(Order::class, Product::class);
    }

    public function works()
    {
        return $this->hasMany(Work::class);
    }

    // Appointments requested by this user (as buyer/requester)
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    // Appointments where this user is the upcycler
    public function appointmentsAsUpcycler()
    {
        return $this->hasMany(Appointment::class, 'upcycler_id');
    }

    // Users that this user has blocked
    public function blockedUsers()
    {
        return $this->hasMany(BlockedUser::class, 'user_id');
    }

    // Users that have blocked this user
    public function blockedByUsers()
    {
        return $this->hasMany(BlockedUser::class, 'blocked_user_id');
    }

    // Check if this user has blocked another user
    public function hasBlocked($userId)
    {
        return $this->blockedUsers()->where('blocked_user_id', $userId)->exists();
    }

    // Check if this user is blocked by another user
    public function isBlockedBy($userId)
    {
        return $this->blockedByUsers()->where('user_id', $userId)->exists();
    }

    public function profileImageUrl()
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */
        $s3 = Storage::disk('s3');

        // If user has a profile picture, return its full S3 URL
        if ($this->profile_pic) {
            return $s3->url($this->profile_pic);
        }

        // Otherwise, return the public default profile image URL
        return 'https://thriftit-bucket-s3.s3.ap-southeast-1.amazonaws.com/profile_pictures/default-profile.jpg';
    }

    public function getIsTopDonorAttribute()
    {
        // You can change this threshold (e.g., 1000) here in one place later
        return $this->points > 50;
    }

    // 2. Logic for Trusted Upcycler Badge
    public function getIsTrustedUpcyclerAttribute()
    {
        // We can access 'eco_posts_count' safely because the Repository loaded it
        return $this->eco_posts_count > 5;
    }
}
