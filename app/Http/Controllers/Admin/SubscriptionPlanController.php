<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of subscription plans
     */
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->get();
        
        return view('admin.subscription-plans.index', [
            'title' => 'Subscription Plans',
            'plans' => $plans,
        ]);
    }

    /**
     * Show the form for creating a new plan
     */
    public function create()
    {
        return view('admin.subscription-plans.create', [
            'title' => 'Create Subscription Plan',
        ]);
    }

    /**
     * Store a newly created plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'template_limit' => 'nullable|integer|min:0',
            'access_premium_templates' => 'boolean',
            'priority_support' => 'boolean',
            'custom_branding' => 'boolean',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['access_premium_templates'] = $request->has('access_premium_templates');
        $validated['priority_support'] = $request->has('priority_support');
        $validated['custom_branding'] = $request->has('custom_branding');
        $validated['is_active'] = $request->has('is_active');

        // Convert features from textarea to array
        if ($request->filled('features_text')) {
            $validated['features'] = array_filter(explode("\n", $request->features_text));
        }

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan created successfully!');
    }

    /**
     * Show the form for editing a plan
     */
    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plans.edit', [
            'title' => 'Edit Subscription Plan',
            'plan' => $subscriptionPlan,
        ]);
    }

    /**
     * Update the specified plan
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'template_limit' => 'nullable|integer|min:0',
            'access_premium_templates' => 'boolean',
            'priority_support' => 'boolean',
            'custom_branding' => 'boolean',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['access_premium_templates'] = $request->has('access_premium_templates');
        $validated['priority_support'] = $request->has('priority_support');
        $validated['custom_branding'] = $request->has('custom_branding');
        $validated['is_active'] = $request->has('is_active');

        // Convert features from textarea to array
        if ($request->filled('features_text')) {
            $validated['features'] = array_filter(explode("\n", $request->features_text));
        }

        $subscriptionPlan->update($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully!');
    }

    /**
     * Remove the specified plan
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        // Don't allow deleting if users are subscribed
        if ($subscriptionPlan->subscriptions()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete plan with active subscriptions!');
        }

        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully!');
    }

    /**
     * Toggle plan status
     */
    public function toggleStatus(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update([
            'is_active' => !$subscriptionPlan->is_active
        ]);

        return back()->with('success', 'Plan status updated successfully!');
    }
}