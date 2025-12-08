<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'data',
        'read_at',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    /**
     * Accessor to ensure is_read is always a proper boolean
     * and that notifications with read_at are always considered read
     */
    public function getIsReadAttribute($value)
    {
        // If read_at is set, the notification is definitely read
        if (isset($this->attributes['read_at']) && $this->attributes['read_at'] !== null) {
            return true;
        }
        // Ensure proper boolean conversion (handle 0/1 from database)
        return (bool) ($value ?? false);
    }

    /**
     * Boot method to ensure data consistency
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure that if read_at is set, is_read is also true
        static::saving(function ($notification) {
            if ($notification->read_at !== null && !$notification->is_read) {
                $notification->is_read = true;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the profile picture URL for the user who triggered the notification
     */
    public function getFromUserProfilePicAttribute()
    {
        $fromUserId = $this->getFromUserId();
        
        if ($fromUserId) {
            $fromUser = User::find($fromUserId);
            if ($fromUser) {
                return $fromUser->profileImageUrl();
            }
        }
        
        return asset('images/default-profile.jpg');
    }

    /**
     * Get the user ID of the person who triggered the notification
     */
    protected function getFromUserId()
    {
        // Try to get from_user_id from data first
        if (isset($this->data['from_user_id'])) {
            return $this->data['from_user_id'];
        }

        // Otherwise, try to get from related models based on type
        switch ($this->type) {
            case 'comment':
            case 'comment_reply':
                if (isset($this->data['comment_id'])) {
                    $comment = \App\Models\Comment::find($this->data['comment_id']);
                    return $comment ? $comment->user_id : null;
                }
                break;
                
            case 'appointment_booked':
                if (isset($this->data['appointment_id'])) {
                    $appointment = \App\Models\Appointment::where('appointmentid', $this->data['appointment_id'])->first();
                    return $appointment ? $appointment->user_id : null;
                }
                break;
                
            case 'product_status':
                if (isset($this->data['product_id'])) {
                    $product = \App\Models\Product::find($this->data['product_id']);
                    return $product ? $product->user_id : null;
                }
                break;
                
            case 'donation_status':
                if (isset($this->data['donation_id'])) {
                    $donation = \App\Models\Donation::find($this->data['donation_id']);
                    return $donation ? $donation->user_id : null;
                }
                break;
        }

        return null;
    }
}
