<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertBooking extends Model
{
    protected $fillable = [
        'user_id',
        'booking_ref',
        'name',
        'email',
        'phone',
        'session_date',
        'duration',
        'session_type',
        'notes',
        'meeting_link',
        'status',
        'admin_notes',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'session_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Generate unique booking reference
     */
    public static function generateBookingRef(): string
    {
        do {
            $ref = 'EXP-' . strtoupper(substr(uniqid(), -8));
        } while (self::where('booking_ref', $ref)->exists());

        return $ref;
    }

    /**
     * Get the user that owns the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Confirm the booking
     */
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Mark booking as completed
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Cancel the booking
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Check if booking is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->session_date->isFuture() && in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if booking is past
     */
    public function isPast(): bool
    {
        return $this->session_date->isPast();
    }
}
