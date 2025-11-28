<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'stripe_price_id',
        'stripe_monthly_price_id',
        'stripe_yearly_price_id',
        'template_limit',
        'access_premium_templates',
        'priority_support',
        'custom_branding',
        'features',
        'is_active',
        'trial_days',
        'sort_order',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'template_limit' => 'integer',
        'trial_days' => 'integer',
        'access_premium_templates' => 'boolean',
        'priority_support' => 'boolean',
        'custom_branding' => 'boolean',
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all subscriptions for this plan
     */
  public function subscriptions()
{
    return $this->hasMany(UserSubscription::class, 'subscription_plan_id');
}

    /**
     * Get active subscriptions count
     */
    public function activeSubscriptionsCount()
    {
        return $this->subscriptions()->where('status', 'active')->count();
    }

    /**
     * Get price based on billing period
     */
    public function getPrice($period = 'monthly')
    {
        return $period === 'yearly' ? $this->yearly_price : $this->monthly_price;
    }

    /**
     * Calculate yearly savings
     */
    public function getYearlySavings()
    {
        $monthlyTotal = $this->monthly_price * 12;
        return $monthlyTotal - $this->yearly_price;
    }

    /**
     * Get savings percentage
     */
    public function getSavingsPercentage()
    {
        $monthlyTotal = $this->monthly_price * 12;
        if ($monthlyTotal == 0) return 0;
        return round((($monthlyTotal - $this->yearly_price) / $monthlyTotal) * 100);
    }

    /**
     * Check if plan is free
     */
    public function isFree()
    {
        return $this->slug === 'free' || ($this->monthly_price == 0 && $this->yearly_price == 0);
    }
}
