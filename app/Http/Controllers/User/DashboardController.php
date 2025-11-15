<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserResume;
use App\Models\CoverLetter;
use App\Models\UserSubscription;
use App\Models\Template;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's active subscription
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->with('subscriptionPlan') // Load the plan relationship
            ->latest()
            ->first();
        
        // Calculate days until next billing (for active subscriptions)
        $trialDaysRemaining = null;
        $daysUntilBilling = null;
        
        if ($subscription && $subscription->status === 'active' && $subscription->next_billing_date) {
            $nextBilling = Carbon::parse($subscription->next_billing_date);
            $now = Carbon::now();
            $daysUntilBilling = $now->diffInDays($nextBilling, false);
            
            if ($daysUntilBilling < 0) {
                $daysUntilBilling = 0;
            }
        }
        
        // Get statistics (removed is_deleted check)
        $stats = [
            'total_resumes' => UserResume::where('user_id', $user->id)->count(),
            
            'total_cover_letters' => CoverLetter::where('user_id', $user->id)->count(),
            
            'subscription_status' => $subscription ? $subscription->status : 'none',
            
            'subscription_plan' => $subscription && $subscription->subscriptionPlan 
                ? $subscription->subscriptionPlan->name 
                : null,
            
            'billing_period' => $subscription ? $subscription->billing_period : null,
            
            'next_billing_date' => $subscription && $subscription->next_billing_date 
                ? Carbon::parse($subscription->next_billing_date)->format('M d, Y')
                : null,
        ];
        
        // Get recent resumes (last 5) - removed is_deleted check
        $recentResumes = UserResume::where('user_id', $user->id)
            ->with('template')
            ->latest()
            ->take(5)
            ->get();
        
        // Get available templates count
        $availableTemplatesCount = Template::where('is_active', true)->count();
        
        // Check if user has premium access (active subscription)
        $hasPremiumAccess = $subscription && $subscription->status === 'active';
        
        return view('user.dashboard.index', compact(
            'user',
            'subscription',
            'stats',
            'recentResumes',
            'trialDaysRemaining',
            'daysUntilBilling',
            'availableTemplatesCount',
            'hasPremiumAccess'
        ));
    }
    
    /**
     * Get dashboard stats for AJAX requests
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_resumes' => UserResume::where('user_id', $user->id)->count(),
            
            'total_cover_letters' => CoverLetter::where('user_id', $user->id)->count(),
            
            'templates_used' => UserResume::where('user_id', $user->id)
                ->distinct('template_id')
                ->count('template_id'),
        ];
        
        return response()->json($stats);
    }
}