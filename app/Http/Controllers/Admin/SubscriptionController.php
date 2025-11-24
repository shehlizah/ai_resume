<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions
     */
    public function index(Request $request)
    {
        $query = UserSubscription::with(['user', 'plan']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by user name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by plan
        if ($request->has('plan') && $request->plan !== 'all') {
            $query->where('subscription_plan_id', $request->plan);
        }

        $subscriptions = $query->latest()->paginate(20);

        // Get all plans for filter dropdown
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'plans'));
    }

    /**
     * Display the specified subscription
     */
    public function show(UserSubscription $subscription)
    {
        $subscription->load(['user', 'plan', 'payments']);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Cancel a subscription
     */
    public function cancel(UserSubscription $subscription)
    {
        try {
            if ($subscription->cancel(true)) {
                return back()->with('success', 'Subscription cancelled successfully.');
            }

            return back()->with('error', 'Failed to cancel subscription.');
        } catch (\Exception $e) {
            \Log::error('Admin subscription cancellation failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while cancelling the subscription.');
        }
    }

    /**
     * Activate a subscription
     */
    public function activate(UserSubscription $subscription)
    {
        try {
            $subscription->update([
                'status' => 'active',
                'auto_renew' => true,
            ]);

            return back()->with('success', 'Subscription activated successfully.');
        } catch (\Exception $e) {
            \Log::error('Admin subscription activation failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while activating the subscription.');
        }
    }
}