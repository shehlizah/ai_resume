<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $table = 'abandoned_carts';

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'session_data',
        'resume_id',
        'recovery_email_sent_count',
        'first_recovery_email_at',
        'completed_at',
    ];

    protected $casts = [
        'session_data' => 'array',
        'first_recovery_email_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this abandoned cart
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if cart has been abandoned for more than X hours
     */
    public function isAbandonedFor($hours = 1)
    {
        return $this->created_at->addHours($hours)->isPast();
    }

    /**
     * Check if recovery email should be sent
     */
    public function shouldSendRecoveryEmail()
    {
        // Only send if abandoned and not yet completed
        if ($this->status !== 'abandoned') {
            return false;
        }

        // First email after 1 hour
        if ($this->recovery_email_sent_count === 0 && $this->isAbandonedFor(1)) {
            return true;
        }

        // Second email after 24 hours
        if ($this->recovery_email_sent_count === 1 && $this->isAbandonedFor(24)) {
            return true;
        }

        // Don't send more than 2 emails
        return false;
    }

    /**
     * Mark recovery email as sent
     */
    public function markRecoveryEmailSent()
    {
        $this->recovery_email_sent_count += 1;

        if ($this->first_recovery_email_at === null) {
            $this->first_recovery_email_at = now();
        }

        $this->save();
    }

    /**
     * Mark cart as completed/recovered
     */
    public function markCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Get pending recovery carts
     */
    public static function getPendingRecovery()
    {
        return self::where('status', 'abandoned')
            ->where('user_id', '!=', null)
            ->where(function ($query) {
                // Abandoned for at least 1 hour
                $query->where('created_at', '<', now()->subHours(1));
            })
            ->get()
            ->filter(fn($cart) => $cart->shouldSendRecoveryEmail());
    }
};
