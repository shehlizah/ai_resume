<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Subscription as StripeSubscription;

class PaymentController extends Controller
{
    /**
     * Process payment with Stripe
     */
    public function stripeCheckout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_period' => 'required|in:monthly,yearly',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $billingPeriod = $request->billing_period;
        $amount = $plan->getPrice($billingPeriod);

        // Don't process if free plan
        if ($amount == 0) {
            return $this->activateFreePlan($plan);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $plan->name . ' Plan',
                            'description' => $plan->description,
                        ],
                        'unit_amount' => $amount * 100, // Convert to cents
                        'recurring' => [
                            'interval' => $billingPeriod === 'yearly' ? 'year' : 'month',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('user.payment.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('user.pricing'),
                'client_reference_id' => auth()->id(),
                'metadata' => [
                    'user_id' => auth()->id(),
                    'plan_id' => $plan->id,
                    'billing_period' => $billingPeriod,
                ],
            ];

            // Add trial period if plan has trial days
            if (isset($plan->trial_days) && $plan->trial_days > 0) {
                $sessionData['subscription_data'] = [
                    'trial_period_days' => $plan->trial_days,
                ];
            }

            $session = StripeSession::create($sessionData);

            return redirect($session->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle Stripe success callback
     */
    public function stripeSuccess(Request $request)
    {
        if (!$request->has('session_id')) {
            return redirect()->route('user.pricing')->with('error', 'Invalid session.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = StripeSession::retrieve($request->session_id);

            if ($session->payment_status === 'paid' || $session->payment_status === 'unpaid') {
                $metadata = $session->metadata;
                $plan = SubscriptionPlan::findOrFail($metadata['plan_id']);
                
                // Fetch the full subscription details from Stripe
                $stripeSubscription = null;
                $trialEnd = null;
                
                if ($session->subscription) {
                    $stripeSubscription = StripeSubscription::retrieve($session->subscription);
                    
                    // Extract trial end date if exists
                    if ($stripeSubscription->trial_end) {
                        $trialEnd = Carbon::createFromTimestamp($stripeSubscription->trial_end);
                    }
                }
                
                // Create subscription with trial info
                $subscription = $this->createSubscription(
                    $plan,
                    $metadata['billing_period'],
                    'stripe',
                    $session->subscription,
                    $trialEnd
                );

                // Get transaction ID
                $transactionId = $session->payment_intent ?? $session->subscription ?? $session->id;

                // Create payment record only if actually paid
                if ($session->payment_status === 'paid') {
                    Payment::create([
                        'user_id' => auth()->id(),
                        'user_subscription_id' => $subscription->id,
                        'transaction_id' => $transactionId,
                        'payment_gateway' => 'stripe',
                        'amount' => $session->amount_total / 100,
                        'currency' => strtoupper($session->currency),
                        'status' => 'completed',
                        'payment_type' => 'subscription',
                        'description' => "Subscription to {$plan->name} plan",
                        'metadata' => [
                            'session_id' => $session->id,
                            'customer_id' => $session->customer,
                            'subscription_id' => $session->subscription,
                        ],
                        'paid_at' => now(),
                    ]);
                }

                $message = $trialEnd 
                    ? "Trial started! Your subscription will begin on " . $trialEnd->format('M d, Y')
                    : "Payment successful! Your subscription is now active.";

                return redirect()->route('user.subscription.dashboard')
                    ->with('success', $message);
            }

            return redirect()->route('user.pricing')
                ->with('error', 'Payment was not completed.');

        } catch (\Exception $e) {
            return redirect()->route('user.pricing')
                ->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    /**
     * Process payment with PayPal
     */
    public function paypalCheckout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_period' => 'required|in:monthly,yearly',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $billingPeriod = $request->billing_period;
        $amount = $plan->getPrice($billingPeriod);

        // Don't process if free plan
        if ($amount == 0) {
            return $this->activateFreePlan($plan);
        }

        // Store pending subscription data in session
        session([
            'pending_subscription' => [
                'plan_id' => $plan->id,
                'billing_period' => $billingPeriod,
                'amount' => $amount,
            ]
        ]);

        return view('user.subscription.paypal-checkout', [
            'title' => 'PayPal Checkout',
            'plan' => $plan,
            'amount' => $amount,
            'billingPeriod' => $billingPeriod,
        ]);
    }

    /**
     * Handle PayPal success callback
     */
    public function paypalSuccess(Request $request)
    {
        $request->validate([
            'orderID' => 'required',
            'subscriptionID' => 'nullable',
        ]);

        $pendingSubscription = session('pending_subscription');
        
        if (!$pendingSubscription) {
            return redirect()->route('user.pricing')
                ->with('error', 'Session expired. Please try again.');
        }

        $plan = SubscriptionPlan::findOrFail($pendingSubscription['plan_id']);

        // Create subscription (PayPal doesn't have built-in trial handling here)
        $subscription = $this->createSubscription(
            $plan,
            $pendingSubscription['billing_period'],
            'paypal',
            $request->subscriptionID
        );

        // Create payment record
        Payment::create([
            'user_id' => auth()->id(),
            'user_subscription_id' => $subscription->id,
            'transaction_id' => $request->orderID,
            'payment_gateway' => 'paypal',
            'amount' => $pendingSubscription['amount'],
            'currency' => 'USD',
            'status' => 'completed',
            'payment_type' => 'subscription',
            'description' => "Subscription to {$plan->name} plan",
            'metadata' => [
                'order_id' => $request->orderID,
                'subscription_id' => $request->subscriptionID,
            ],
            'paid_at' => now(),
        ]);

        session()->forget('pending_subscription');

        return redirect()->route('user.subscription.dashboard')
            ->with('success', 'Payment successful! Your subscription is now active.');
    }

    /**
     * Handle PayPal cancel
     */
    public function paypalCancel()
    {
        session()->forget('pending_subscription');
        
        return redirect()->route('user.pricing')
            ->with('info', 'Payment was canceled.');
    }

    /**
     * Create subscription record
     */
    private function createSubscription($plan, $billingPeriod, $gateway, $gatewaySubscriptionId = null, $trialEnd = null)
    {
        $user = auth()->user();

        // Cancel existing active subscription
        $existingSubscription = $user->activeSubscription()->first();
        if ($existingSubscription) {
            $existingSubscription->cancel();
        }

        $startDate = now();
        
        // If there's a trial, the actual billing starts after trial ends
        if ($trialEnd) {
            $endDate = $trialEnd->copy()->add(
                $billingPeriod === 'yearly' ? '1 year' : '1 month'
            );
        } else {
            $endDate = $billingPeriod === 'yearly' 
                ? $startDate->copy()->addYear() 
                : $startDate->copy()->addMonth();
        }
        
        $autoRenew = $plan->getPrice($billingPeriod) > 0 ? true : false;

        return UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'billing_period' => $billingPeriod,
            'status' => 'active',
            'amount' => $plan->getPrice($billingPeriod),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'trial_ends_at' => $trialEnd, // ✅ NOW SAVING TRIAL DATE
            'next_billing_date' => $trialEnd ?? $endDate,
            'auto_renew' => $autoRenew,
            'payment_gateway' => $gateway,
            'gateway_subscription_id' => $gatewaySubscriptionId,
        ]);
    }

    /**
     * Activate free plan without payment
     */
    private function activateFreePlan($plan)
    {
        // Check if user already availed free plan
        $user = auth()->user();

        $alreadyAvailed = UserSubscription::where('user_id', $user->id)
            ->where('subscription_plan_id', $plan->id)
            ->exists();

        if ($alreadyAvailed) {
            return redirect()->route('user.subscription.dashboard')
                ->with('error', 'Free Plan already availed!');
        }

        // Create free plan subscription
        $subscription = $this->createSubscription($plan, 'monthly', null, null);

        return redirect()->route('user.subscription.dashboard')
            ->with('success', 'Free plan activated successfully!');
    }
}