<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Categories;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;

class Donation extends Model
{
    use Searchable;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'approval_status',
        'size',
        'image',
        'status',
        'segment_id',
        'barangay_id',
        'proof',
        'verification_status',           

    ];


       public function user(){
        return $this->belongsTo(User::class);
    }
    public function category(){
        return $this->belongsTo(Categories::class, 'category_id');
    }
    
     public function setImageAttribute($value)
    {
        if(is_file($value)){
            $this->attributes['image'] = $value->store('donation_images','public');
        }else {
            $this->attributes['image'] = $value;
        }
    }
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    // Method to force fresh comments loading
    public function getFreshComments()
    {
        $this->unsetRelation('comments');
        return $this->load(['comments.user']);
    }

        /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray() : array
    {
        return array_merge($this->toArray(),[
            'id' => (string) $this->id,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp,        
        ]);
    }
    
     public function segment()
     {
        return $this->belongsTo(Segment::class, 'segment_id');
     }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

    public function donationImages()
    {
        return $this->hasMany(DonationImage::class);
    }

    public function getFirstImageAttribute()
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */
        $s3 = Storage::disk('s3');

        $firstImage = $this->donationImages->first()?->image;

        if ($firstImage) {
            return $s3->url($firstImage); // use $s3 variable instead of calling Storage::disk again
        }

        // Default placeholder image (inline SVG to avoid 404 errors)
        return 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\'  height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EDonation%3C/text%3E%3C/svg%3E';
    }
}


    
