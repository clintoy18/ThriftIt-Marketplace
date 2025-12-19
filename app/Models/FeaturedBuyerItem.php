<?php

namespace App\Models; // Check this matches exactly

use Illuminate\Database\Eloquent\Model;

class FeaturedBuyerItem extends Model 
{
    protected $fillable = [
        'featured_buyer_id', 
        'product_name', 
        'price', 
        'size', 
        'category_label', 
        'image_path'
    ];

    public function buyer() 
    {
        return $this->belongsTo(FeaturedBuyer::class);
    }
}