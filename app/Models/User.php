<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
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
     * Relationship: User has many interview sessions
     */
    public function interviewSessions()
    {
        return $this->hasMany(InterviewSession::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is employer
     */
    public function isEmployer()
    {
        return $this->role === 'employer';
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
     * Get employer add-on purchases (for employer users)
     */
    public function employerAddOns()
    {
        return $this->hasMany(EmployerAddOn::class, 'employer_id');
    }

    /**
     * Get employer active add-ons
     */
    public function activeEmployerAddOns()
    {
        return $this->employerAddOns()
            ->where('employer_add_ons.status', 'active')
            ->where(function($query) {
                $query->whereNull('employer_add_ons.expires_at')
                      ->orWhere('employer_add_ons.expires_at', '>', now());
            });
    }

    /**
     * Check if employer has purchased a specific add-on
     */
    public function hasPurchasedEmployerAddOn($addOnId)
    {
        return $this->employerAddOns()
            ->where('add_on_id', $addOnId)
            ->where('employer_add_ons.status', 'active')
            ->where(function($query) {
                $query->whereNull('employer_add_ons.expires_at')
                      ->orWhere('employer_add_ons.expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Check if employer has AI matching add-on
     */
    public function hasAiMatchingAddOn()
    {
        return $this->activeEmployerAddOns()
            ->whereHas('addOn', fn($q) => $q->where('slug', 'ai-matching'))
            ->exists();
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

    /**
     * Check if user has created at least one resume
     */
    public function hasCreatedResume(): bool
    {
        return $this->resumes()->exists();
    }

    /**
     * Check if user has created at least one cover letter
     */
    public function hasCreatedCoverLetter(): bool
    {
        return $this->coverLetters()->exists();
    }

    /**
     * Check if user has used interview prep feature
     */
    public function hasUsedInterviewPrep(): bool
    {
        return $this->interviewSessions()->exists();
    }

    /**
     * Check if user has engaged with job search (session flag or views counter)
     */
    public function hasCompletedJobSearch(): bool
    {
        return session('job_search_completed', false) || session('jobs_viewed', 0) > 0;
    }

    /**
     * Check if user has booked an expert session (session flag)
     */
    public function hasBookedExpertSession(): bool
    {
        return session('book_session_completed', false);
    }

    /**
     * Get the next step popup to show user based on their progress
     * Returns null if user hasn't created resume yet or completed all steps
     */
    public function getNextStepPopup(): ?string
    {
        // Only show popups if user has created at least one resume
        if (!$this->hasCreatedResume()) {
            return null;
        }

        // Determine highest completed step (order matters, later steps override earlier)
        $progressLevel = 1; // Resume completed

        if ($this->hasCreatedCoverLetter()) {
            $progressLevel = 2;
        }

        if ($this->hasUsedInterviewPrep()) {
            $progressLevel = 3;
        }

        if ($this->hasCompletedJobSearch()) {
            $progressLevel = 4;
        }

        if ($this->hasBookedExpertSession()) {
            $progressLevel = 5;
        }

        // Return the next step based on the highest completed level
        return match ($progressLevel) {
            1 => 'cover_letter',
            2 => 'interview_prep',
            3 => 'job_search',
            4 => 'book_session',
            default => null, // All steps completed
        };
    }

}
