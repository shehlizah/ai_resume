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
        
             // Fake subscription creation for testing the view
        // $this->createSubscription($plan, $billingPeriod, 'stripe', 'TEST_SESSION_ID');
    
        // return redirect()->route('user.subscription.dashboard')
        //     ->with('success', 'Fake Stripe subscription created for testing!');
        
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = StripeSession::create([
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
            ]);

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

            if ($session->payment_status === 'paid') {
                $metadata = $session->metadata;
                $plan = SubscriptionPlan::findOrFail($metadata['plan_id']);
                
                // Create subscription
                $subscription = $this->createSubscription(
                    $plan,
                    $metadata['billing_period'],
                    'stripe',
                    $session->subscription
                );

                // Get transaction ID - for subscriptions, use subscription ID or session ID
                $transactionId = $session->payment_intent ?? $session->subscription ?? $session->id;

                // Create payment record
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

                return redirect()->route('user.subscription.dashboard')
                    ->with('success', 'Payment successful! Your subscription is now active.');
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

        // Create subscription
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
    private function createSubscription($plan, $billingPeriod, $gateway, $gatewaySubscriptionId = null)
    {
        $user = auth()->user();

        // Cancel existing active subscription
        $existingSubscription = $user->activeSubscription()->first();
        if ($existingSubscription) {
            $existingSubscription->cancel();
        }

        $startDate = now();
        $endDate = $billingPeriod === 'yearly' 
            ? $startDate->copy()->addYear() 
            : $startDate->copy()->addMonth();
        
        $autoRenew = $plan->getPrice($billingPeriod) > 0 ? true : false;

        return UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'billing_period' => $billingPeriod,
            'status' => 'active',
            'amount' => $plan->getPrice($billingPeriod),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'next_billing_date' => $endDate,
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

    $alreadyAvailed = \App\Models\UserSubscription::where('user_id', $user->id)
        ->where('subscription_plan_id', '1') // adjust if your column name is different
        ->exists();

    if ($alreadyAvailed) {
        return redirect()->route('user.subscription.dashboard')
            ->with('error', 'Free Plan already availed!');
    }

    // Otherwise, create the free plan subscription
    $subscription = $this->createSubscription($plan, 'monthly', null, null);

    return redirect()->route('user.subscription.dashboard')
        ->with('success', 'Free plan activated successfully!');
}


    // private function activateFreePlan($plan)
    // {
    //     $subscription = $this->createSubscription($plan, 'monthly', null, null);

    //     return redirect()->route('user.subscription.dashboard')
    //         ->with('success', 'Free plan activated successfully!');
    // }
}