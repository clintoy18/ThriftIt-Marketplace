<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDisclosure extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'agreed_at',
        'ip_address',
        'version',
    ];

    protected $casts = [
        'agreed_at' => 'datetime',
    ];

    /**
     * Get the user who agreed to the disclosure
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with this disclosure agreement
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
