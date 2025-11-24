<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddOn extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'add_on_id',
        'amount_paid',
        'payment_gateway',
        'payment_id',
        'status',
        'purchased_at',
        'expires_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the add-on
     */
    public function addOn()
    {
        return $this->belongsTo(AddOn::class);
    }

    /**
     * Check if active
     */
    public function isActive()
    {
        return $this->status === 'active' && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Get status color
     */
    public function getStatusColor()
    {
        return match($this->status) {
            'active' => 'success',
            'expired' => 'danger',
            'cancelled' => 'warning',
            default => 'secondary'
        };
    }
}