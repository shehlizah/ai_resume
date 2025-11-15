<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $planSlug = null): Response
    {
        $user = $request->user();

        // If no user, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's current subscription
        $subscription = $user->activeSubscription()->first();
        $currentPlan = $subscription ? $subscription->plan : null;

        // If no active subscription, use free plan
        if (!$currentPlan) {
            $currentPlan = \App\Models\SubscriptionPlan::where('slug', 'free')->first();
        }

        // If specific plan is required
        if ($planSlug) {
            $requiredPlans = explode('|', $planSlug); // Support multiple plans like: basic|premium
            
            if (!in_array($currentPlan->slug, $requiredPlans)) {
                return redirect()->route('user.pricing')
                    ->with('error', 'This feature requires a ' . implode(' or ', $requiredPlans) . ' subscription.');
            }
        }

        // Add plan info to request for easy access
        $request->attributes->set('current_plan', $currentPlan);
        $request->attributes->set('subscription', $subscription);

        return $next($request);
    }
}