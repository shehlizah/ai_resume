<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display pricing plans
     */
    public function pricing()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $currentSubscription = auth()->user()->activeSubscription()->first();

        return view('user.pricing', [
            'title' => 'Subscription Plans',
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
        ]);
    }

    /**
     * Display user's subscription dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        $currentSubscription = $user->activeSubscription()->with('plan')->first();
        $subscriptionHistory = $user->subscriptions()->with('plan')->latest()->paginate(10);
        $recentPayments = $user->payments()->with('subscription')->latest()->take(5)->get();
        
        return view('user.subscription.dashboard', [
            'title' => 'My Subscription',
            'user' => $user,
            'currentSubscription' => $currentSubscription,
            'subscriptionHistory' => $subscriptionHistory,
            'recentPayments' => $recentPayments,
        ]);
    }

    /**
     * Show checkout page for selected plan
     */
    public function checkout(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'billing_period' => 'required|in:monthly,yearly',
        ]);

        $billingPeriod = $request->billing_period;
        $amount = $plan->getPrice($billingPeriod);

        // Check if user already has active subscription
        $currentSubscription = auth()->user()->activeSubscription()->first();
        
        if ($currentSubscription && $currentSubscription->plan_id == $plan->id) {
            return redirect()->route('user.subscription.dashboard')
                ->with('info', 'You already have this subscription plan.');
        }

        return view('user.subscription.checkout', [
            'title' => 'Checkout',
            'plan' => $plan,
            'billingPeriod' => $billingPeriod,
            'amount' => $amount,
            'currentSubscription' => $currentSubscription,
        ]);
    }

    /**
     * Cancel subscription
     */
     /**
 * Cancel subscription
 */
public function cancel(Request $request)
{
    $user = auth()->user();
    $subscription = $user->activeSubscription;

    if (!$subscription) {
        return redirect()->route('user.subscription.dashboard')
            ->with('error', 'No active subscription found.');
    }

    // Check if already canceled
    if ($subscription->status === 'canceled') {
        return redirect()->route('user.subscription.dashboard')
            ->with('info', 'This subscription is already canceled.');
    }

    // Validate cancellation reason (optional)
    $request->validate([
        'reason' => 'nullable|string|max:500',
    ]);

    // Get the cancellation type
    $immediately = $request->input('immediately', false);
    
    // Check if in trial before canceling
    $isInTrial = $subscription->isInTrial();

    // Call the MODEL's cancel method
    if ($subscription->cancel($immediately)) {
        // Refresh to get updated values
        $subscription->refresh();
        
        // Optionally store cancellation reason
        if ($request->filled('reason')) {
            // Store in metadata or separate table
        }

        // Different messages for trial vs paid subscriptions
        if ($isInTrial) {
            if ($immediately) {
                return redirect()->route('user.subscription.dashboard')
                    ->with('success', 'Your trial has been canceled immediately.');
            } else {
                $endDate = $subscription->trial_end_date ?? $subscription->end_date;
                return redirect()->route('user.subscription.dashboard')
                    ->with('success', 'Your trial has been canceled. You can continue using it until ' . 
                           $endDate->format('F j, Y') . '. You will not be charged.');
            }
        } else {
            if ($immediately) {
                return redirect()->route('user.subscription.dashboard')
                    ->with('success', 'Your subscription has been canceled immediately.');
            } else {
                return redirect()->route('user.subscription.dashboard')
                    ->with('success', 'Your subscription has been canceled. You can continue using it until ' . 
                           $subscription->end_date->format('F j, Y'));
            }
        }
    }

    return redirect()->route('user.subscription.dashboard')
        ->with('error', 'Failed to cancel subscription. Please try again or contact support.');
}

    public function xcancel(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return redirect()->route('user.subscription.dashboard')
                ->with('error', 'No active subscription found.');
        }

        // Check if already canceled
        if ($subscription->status === 'canceled') {
            return redirect()->route('user.subscription.dashboard')
                ->with('info', 'This subscription is already canceled.');
        }

        // Validate cancellation reason (optional)
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        // Get the cancellation type (immediately or at period end)
        $immediately = $request->input('immediately', false);

        // Call the MODEL's cancel method
        if ($subscription->cancel($immediately)) {
            // Refresh to get updated values
            $subscription->refresh();
            
            // Optionally store cancellation reason
            if ($request->filled('reason')) {
                // You could store this in a separate table or in metadata
            }

            if ($immediately) {
                return redirect()->route('user.subscription.dashboard')
                    ->with('success', 'Your subscription has been canceled immediately.');
            } else {
                return redirect()->route('user.subscription.dashboard')
                    ->with('success', 'Your subscription has been canceled. You can continue using it until ' . 
                           $subscription->end_date->format('F j, Y'));
            }
        }

        return redirect()->route('user.subscription.dashboard')
            ->with('error', 'Failed to cancel subscription. Please try again or contact support.');
    }

    /**
     * Resume canceled subscription
     */
    public function resume()
    {
        $subscription = auth()->user()->subscriptions()
            ->where('status', 'canceled')
            ->where('end_date', '>=', now())
            ->latest()
            ->first();

        if (!$subscription) {
            return back()->with('error', 'No canceled subscription found to resume.');
        }

        $subscription->update([
            'status' => 'active',
            'auto_renew' => true,
        ]);

        return back()->with('success', 'Your subscription has been resumed!');
    }

    /**
     * Change billing period
     */
    public function changeBillingPeriod(Request $request)
    {
        $request->validate([
            'billing_period' => 'required|in:monthly,yearly',
        ]);

        $subscription = auth()->user()->activeSubscription()->first();

        if (!$subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        $newPeriod = $request->billing_period;
        $newAmount = $subscription->plan->getPrice($newPeriod);

        // Update will take effect on next billing cycle
        $subscription->update([
            'billing_period' => $newPeriod,
            'amount' => $newAmount,
        ]);

        return back()->with('success', 'Billing period will be changed to ' . $newPeriod . ' on your next billing date.');
    }
}