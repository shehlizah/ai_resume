<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerAddOn extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
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

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function addOn()
    {
        return $this->belongsTo(AddOn::class);
    }

    public function isActive()
    {
        return $this->status === 'active' &&
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

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
