<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserSubscriptionController extends Controller
{
    /**
     * Display all user subscriptions
     */
    public function index(Request $request)
    {
        $query = UserSubscription::with(['user', 'plan']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan_id')) {
            $query->where('subscription_plan_id', $request->plan_id);
        }

        // Search by user
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $subscriptions = $query->latest()->paginate(20);
        $plans = SubscriptionPlan::all();
        
        // Statistics
        $stats = [
            'total' => UserSubscription::count(),
            'active' => UserSubscription::where('status', 'active')->count(),
            'expired' => UserSubscription::where('status', 'expired')->count(),
            'canceled' => UserSubscription::where('status', 'canceled')->count(),
            'revenue_monthly' => UserSubscription::where('status', 'active')
                ->where('billing_period', 'monthly')
                ->sum('amount'),
            'revenue_yearly' => UserSubscription::where('status', 'active')
                ->where('billing_period', 'yearly')
                ->sum('amount'),
        ];

        return view('admin.user-subscriptions.index', [
            'title' => 'User Subscriptions',
            'subscriptions' => $subscriptions,
            'plans' => $plans,
            'stats' => $stats,
        ]);
    }

    /**
     * Show subscription details
     */
    public function show(UserSubscription $userSubscription)
    {
        $userSubscription->load(['user', 'plan', 'payments']);

        return view('admin.user-subscriptions.show', [
            'title' => 'Subscription Details',
            'subscription' => $userSubscription,
        ]);
    }

    /**
     * Cancel a subscription
     */
    public function cancel(UserSubscription $userSubscription)
    {
        $userSubscription->cancel();

        return back()->with('success', 'Subscription canceled successfully!');
    }

    /**
     * Reactivate a subscription
     */
    public function reactivate(UserSubscription $userSubscription)
    {
        $userSubscription->update([
            'status' => 'active',
            'auto_renew' => true,
        ]);

        return back()->with('success', 'Subscription reactivated successfully!');
    }

    /**
     * Extend subscription end date
     */
    public function extend(Request $request, UserSubscription $userSubscription)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $userSubscription->update([
            'end_date' => $userSubscription->end_date->addDays($request->days),
            'next_billing_date' => $userSubscription->next_billing_date 
                ? $userSubscription->next_billing_date->addDays($request->days) 
                : null,
        ]);

        return back()->with('success', "Subscription extended by {$request->days} days!");
    }
    
    public function daysRemaining()
{
    $now = now();

    // Compare with end_date at end of day
    $end = $this->end_date->copy()->endOfDay();

    if ($now->greaterThan($end)) {
        return 0;
    }

    return $now->diffInDays($end);
}

}