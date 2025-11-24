<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_subscriptions';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'billing_period',
        'status',
        'amount',
        'start_date',
        'end_date',
        'next_billing_date',
            'trial_end_date' ,
        'auto_renew',
        'payment_gateway',
        'gateway_subscription_id',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
            'trial_end_date' => 'date',  // ✅ MAKE SURE THIS LINE EXISTS

        'next_billing_date' => 'date',
        'auto_renew' => 'boolean',
        'amount' => 'decimal:2',
    ];
    
    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the subscription plan.
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
    
    /**
     * Alias for subscriptionPlan relationship.
     */
    public function plan()
    {
        return $this->subscriptionPlan();
    }
    
    /**
     * Check if subscription is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
    
    /**
     * Check if subscription is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->status === 'expired';
    }
    
    /**
     * Check if subscription is canceled.
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->status === 'canceled';
    }
    
    /**
     * Check if subscription is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
    
    /**
     * Scope to get active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Scope to get subscriptions by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    public function getStatusColor()
    {
        return match($this->status) {
            'active' => 'success',
            'expired' => 'danger',
            'cancelled' => 'warning',
            'canceled' => 'warning',
            'pending' => 'info',
            default => 'secondary'
        };
    }
    
    public function isExpiringSoon($days = 7)
    {
        if ($this->status !== 'active' || !$this->end_date) {
            return false;
        }
        return $this->end_date->isFuture() && 
               $this->end_date->diffInDays(now()) <= $days;
    }
    
    /**
     * Get days remaining until subscription ends.
     *
     * @return int
     */
    public function daysRemaining()
    {
        if (!$this->end_date || $this->end_date->isPast()) {
            return 0;
        }
        return $this->end_date->diffInDays(now());
    }
    
/**
 * Cancel the subscription in Stripe and update local database.
 *
 * @param bool $immediately Whether to cancel immediately or at period end
 * @return bool
 */
public function xcancel($immediately = false)  // ✅ Should be boolean, NOT Request
{
    \DB::beginTransaction();
    
    try {
        // Cancel in Stripe if gateway is stripe and gateway_subscription_id exists
        if ($this->payment_gateway === 'stripe' && $this->gateway_subscription_id) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            if ($immediately) {
                // Cancel immediately
                \Stripe\Subscription::update(
                    $this->gateway_subscription_id,
                    ['cancel_at_period_end' => false]
                );
                \Stripe\Subscription::cancel($this->gateway_subscription_id);
                
                // Update local status
                $this->status = 'canceled';
                $this->end_date = now();
                $this->auto_renew = false;
                $this->save();
            } else {
                // Cancel at period end - UPDATE the subscription, don't cancel it
                \Stripe\Subscription::update(
                    $this->gateway_subscription_id,
                    ['cancel_at_period_end' => true]
                );
                
                // Update local status but keep end_date
                $this->status = 'canceled';
                $this->auto_renew = false;
                $this->save();
            }
        } else {
            // No Stripe subscription or different gateway, just update local
            $this->status = 'canceled';
            $this->end_date = $immediately ? now() : $this->end_date;
            $this->auto_renew = false;
            $this->save();
        }
        
        \DB::commit();
        
        \Log::info('Subscription canceled successfully', [
            'subscription_id' => $this->id,
            'user_id' => $this->user_id,
            'immediately' => $immediately,
            'new_status' => $this->status,
            'end_date' => $this->end_date,
        ]);

        return true;
    } catch (\Stripe\Exception\ApiErrorException $e) {
        \DB::rollBack();
        
        \Log::error('Stripe API error during cancellation: ' . $e->getMessage(), [
            'subscription_id' => $this->id,
            'user_id' => $this->user_id,
            'stripe_error' => $e->getError(),
        ]);
        
        return false;
    } catch (\Exception $e) {
        \DB::rollBack();
        
        \Log::error('Subscription cancellation failed: ' . $e->getMessage(), [
            'subscription_id' => $this->id,
            'user_id' => $this->user_id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        
        return false;
    }
}

/**
 * Cancel the subscription in Stripe and update local database.
 * Handles both trial and paid subscriptions appropriately.
 *
 * @param bool $immediately Whether to cancel immediately or at period end
 * @return bool
 */
public function cancel($immediately = false)
{
    \DB::beginTransaction();
    
    try {
        // For trial subscriptions, handle them specially unless canceling immediately
        if ($this->isInTrial() && !$immediately) {
            return $this->cancelAtTrialEnd();
        }
        
        // Cancel in Stripe if gateway is stripe and gateway_subscription_id exists
        if ($this->payment_gateway === 'stripe' && $this->gateway_subscription_id) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            if ($immediately) {
                // Cancel immediately
                \Stripe\Subscription::update(
                    $this->gateway_subscription_id,
                    ['cancel_at_period_end' => false]
                );
                \Stripe\Subscription::cancel($this->gateway_subscription_id);
                
                // Update local status
                $this->status = 'canceled';
                $this->end_date = now();
                $this->auto_renew = false;
                $this->save();
            } else {
                // Cancel at period end - UPDATE the subscription, don't cancel it
                \Stripe\Subscription::update(
                    $this->gateway_subscription_id,
                    ['cancel_at_period_end' => true]
                );
                
                // Update local status but keep end_date
                $this->status = 'canceled';
                $this->auto_renew = false;
                $this->save();
            }
        } else {
            // No Stripe subscription or different gateway, just update local
            $this->status = 'canceled';
            $this->end_date = $immediately ? now() : $this->end_date;
            $this->auto_renew = false;
            $this->save();
        }
        
        \DB::commit();
        
        \Log::info('Subscription canceled successfully', [
            'subscription_id' => $this->id,
            'user_id' => $this->user_id,
            'immediately' => $immediately,
            'was_trial' => $this->isInTrial(),
            'new_status' => $this->status,
            'end_date' => $this->end_date,
        ]);

        return true;
    } catch (\Stripe\Exception\ApiErrorException $e) {
        \DB::rollBack();
        
        \Log::error('Stripe API error during cancellation: ' . $e->getMessage(), [
            'subscription_id' => $this->id,
            'user_id' => $this->user_id,
            'stripe_error' => $e->getError(),
        ]);
        
        return false;
    } catch (\Exception $e) {
        \DB::rollBack();
        
        \Log::error('Subscription cancellation failed: ' . $e->getMessage(), [
            'subscription_id' => $this->id,
            'user_id' => $this->user_id,
            'error' => $e->getMessage(),
        ]);
        
        return false;
    }
}

/**
 * Check if subscription is currently in trial period
 *
 * @return bool
 */
/**
 * Check if subscription is currently in trial period
 *
 * @return bool
 */
public function isInTrial()
{
    if (!$this->trial_end_date) {
        return false;
    }
    
    // Ensure it's a Carbon instance
    $trialEndDate = $this->trial_end_date instanceof \Carbon\Carbon 
        ? $this->trial_end_date 
        : \Carbon\Carbon::parse($this->trial_end_date);
    
    return $trialEndDate->isFuture() && $this->status === 'active';
}

/**
 * Get trial days remaining
 *
 * @return int
 */
/**
 * Get trial days remaining
 *
 * @return int
 */
public function trialDaysRemaining()
{
    try {
        if (!$this->trial_end_date) {
            return 0;
        }
        
        // Convert to Carbon if it's a string
        if (is_string($this->trial_end_date)) {
            $trialEndDate = \Carbon\Carbon::parse($this->trial_end_date);
        } else {
            $trialEndDate = $this->trial_end_date;
        }
        
        // If trial has expired, return 0
        if (now()->greaterThan($trialEndDate)) {
            return 0;
        }
        
        // Get days remaining as integer (no decimals)
        $daysRemaining = (int) now()->diffInDays($trialEndDate, false);
        
        // Ensure positive number, take only first 2 digits
        $daysRemaining = abs($daysRemaining);
        $daysRemainingDisplay = (int) substr((string)$daysRemaining, 0, 2);
        
        return max(0, $daysRemainingDisplay);
        
    } catch (\Exception $e) {
        \Log::error('Error calculating trial days: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Cancel subscription at trial end (prevents conversion to paid)
 *
 * @return bool
 */
protected function cancelAtTrialEnd()
{
    \DB::beginTransaction();
    
    try {
        if ($this->payment_gateway === 'stripe' && $this->gateway_subscription_id) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            // For trials, cancel at trial end (prevent conversion to paid)
            \Stripe\Subscription::update(
                $this->gateway_subscription_id,
                ['cancel_at_period_end' => true]
            );
        }
        
        // Update local status
        $this->status = 'canceled';
        $this->auto_renew = false;
        $this->end_date = $this->trial_end_date ?? $this->end_date;
        $this->save();
        
        \DB::commit();
        
        \Log::info('Trial canceled successfully', [
            'subscription_id' => $this->id,
            'user_id' => $this->user_id,
            'trial_end_date' => $this->trial_end_date,
        ]);
        
        return true;
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Trial cancellation failed: ' . $e->getMessage());
        return false;
    }
}

}