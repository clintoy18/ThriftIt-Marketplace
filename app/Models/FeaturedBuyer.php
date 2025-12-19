<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedBuyer extends Model
{
    protected $fillable = ['user_id', 'name', 'handle', 'follower_count', 'bio', 'testimonial', 'avatar_path'];

    public function items()
    {
        return $this->hasMany(FeaturedBuyerItem::class);
    }
}
