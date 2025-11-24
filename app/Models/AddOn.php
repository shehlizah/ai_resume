<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'type',
        'is_active',
        'features',
        'content',
        'sort_order',
        'icon',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'content' => 'array',
        'price' => 'decimal:2',
    ];

    /**
     * Get users who purchased this add-on
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_add_ons')
            ->withPivot(['amount_paid', 'payment_gateway', 'payment_id', 'status', 'purchased_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Get user add-on purchases
     */
    public function userAddOns()
    {
        return $this->hasMany(UserAddOn::class);
    }

    /**
     * Get active add-on purchases count
     */
    public function activePurchases()
    {
        return $this->userAddOns()->where('status', 'active');
    }

    /**
     * Scope to get only active add-ons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this is a job links add-on
     */
    public function isJobLinks()
    {
        return $this->type === 'job_links';
    }

    /**
     * Check if this is an interview prep add-on
     */
    public function isInterviewPrep()
    {
        return $this->type === 'interview_prep';
    }
}