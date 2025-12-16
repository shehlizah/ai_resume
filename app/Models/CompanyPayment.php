<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_type',
        'item_slug',
        'item_name',
        'amount',
        'payment_method',
        'status',
        'payment_proof',
        'bank_account_name',
        'transfer_date',
        'stripe_session_id',
        'stripe_payment_intent',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transfer_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
