<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Http\Middleware\CheckActivePackage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'has_lifetime_access',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'has_lifetime_access' => 'boolean',
        ];
    }

    /**
     * Relationship: User has many resumes
     */
    public function resumes()
    {
        return $this->hasMany(UserResume::class);
    }

    /**
     * Relationship: User has many cover letters
     */
    public function coverLetters()
    {
        return $this->hasMany(CoverLetter::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Scope: Get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get only admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope: Get only regular users
     */
    public function scopeRegularUsers($query)
    {
        return $query->where('role', 'user');
    }


/**
 * Get user's subscriptions
 */
public function subscriptions()
{
    return $this->hasMany(UserSubscription::class);
}

/**
 * Get user's active subscription
 */
public function activeSubscription()
{
    return $this->hasOne(UserSubscription::class)
        ->where('status', 'active')
        ->where('end_date', '>=', now())
        ->latest();
}

/**
 * Get user's payments
 */
public function payments()
{
    return $this->hasMany(Payment::class);
}

/**
 * Check if user has active subscription
 */
public function hasActiveSubscription()
{
    return $this->activeSubscription()->exists();
}

/**
 * Get user's current plan
 */
public function currentPlan()
{
    $subscription = $this->activeSubscription()->first();
    return $subscription ? $subscription->plan : SubscriptionPlan::where('slug', 'free')->first();
}

/**
 * Check if user can access template based on plan
 */
public function canAccessTemplate($template)
{
    $plan = $this->currentPlan();

    // If template is free, everyone can access
    if ($template->plan_type === 'free') {
        return true;
    }

    // Check plan permissions
    if ($template->plan_type === 'premium' && !$plan->access_premium_templates) {
        return false;
    }

    if ($template->plan_type === 'basic' && $plan->slug === 'free') {
        return false;
    }

    return true;
}

/**
 * Check if user has reached template limit
 */
public function hasReachedTemplateLimit()
{
    $plan = $this->currentPlan();

    // If unlimited (null), return false
    if ($plan->template_limit === null) {
        return false;
    }

    // Count user's created resumes (assuming you have a resumes table)
    // Adjust this based on your actual resume tracking
    $resumeCount = $this->resumes()->count();

    return $resumeCount >= $plan->template_limit;
}

/**
 * Get user's add-on purchases
 */
public function addOns()
{
    return $this->belongsToMany(AddOn::class, 'user_add_ons')
        ->withPivot(['amount_paid', 'payment_gateway', 'payment_id', 'status', 'purchased_at', 'expires_at'])
        ->withTimestamps();
}

/**
 * Get user's active add-ons
 */
public function activeAddOns()
{
    return $this->addOns()->wherePivot('status', 'active');
}

/**
 * Get user add-on purchases
 */
public function userAddOns()
{
    return $this->hasMany(UserAddOn::class);
}

/**
 * Check if user has purchased a specific add-on
 */
public function hasPurchasedAddOn($addOnId)
{
    return $this->userAddOns()
        ->where('add_on_id', $addOnId)
        ->where('status', 'active')
        ->exists();
}

/**
 * Check if user has job links add-on
 */
public function hasJobLinksAccess()
{
    return $this->activeAddOns()
        ->where('type', 'job_links')
        ->exists();
}

/**
 * Check if user has interview prep add-on
 */
public function hasInterviewPrepAccess()
{
    return $this->activeAddOns()
        ->where('type', 'interview_prep')
        ->exists();
}

// Add this method
public function activePackage()
{
    return $this->hasOne(UserSubscription::class)
        ->where('status', 'active')
        ->where(function ($query) {
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
        });
}

public function hasActivePackage()
{
    return $this->activePackage()->exists();
}

// User model
// public function hasActivePackage()
// {
//     return $this->package_id &&
//           $this->package_expires_at?->isFuture();
// }

}
