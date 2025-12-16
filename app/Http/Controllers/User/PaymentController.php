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
        \Log::info('=== STRIPE CHECKOUT CALLED ===');

        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_period' => 'required|in:monthly,yearly',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $billingPeriod = $request->billing_period;
        $amount = $plan->getPrice($billingPeriod);

        \Log::info('Plan selected', ['plan_id' => $plan->id, 'plan_name' => $plan->name, 'amount' => $amount]);

        // Don't process if free plan
        if ($amount == 0) {
            \Log::info('Free plan selected, activating...');
            return $this->activateFreePlan($plan);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            \Log::info('Creating Stripe session...');

            // Determine currency based on amount (IDR if > 1000, otherwise USD)
            $currency = $amount >= 1000 ? 'idr' : 'usd';
            $stripeAmount = $currency === 'idr' ? $amount : ($amount * 100); // IDR doesn't use decimals, USD uses cents

            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $plan->name . ' Plan',
                            'description' => $plan->description,
                        ],
                        'unit_amount' => $stripeAmount,
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
                \Log::info('Trial days added', ['trial_days' => $plan->trial_days]);
            }

            $session = StripeSession::create($sessionData);

            \Log::info('Stripe session created', ['session_id' => $session->id, 'url' => $session->url]);

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
        \Log::info('=== STRIPE SUCCESS CALLBACK ===');
        \Log::info('Session ID: ' . ($request->session_id ?? 'MISSING'));

        if (!$request->has('session_id')) {
            \Log::error('No session_id in request');
            return redirect()->route('user.pricing')->with('error', 'Invalid session.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = StripeSession::retrieve($request->session_id);

            \Log::info('Session retrieved from Stripe', ['payment_status' => $session->payment_status]);

            if ($session->payment_status === 'paid' || $session->payment_status === 'unpaid') {
                $metadata = $session->metadata;
                $plan = SubscriptionPlan::findOrFail($metadata['plan_id']);

                \Log::info('Plan found', ['plan_id' => $plan->id, 'plan_name' => $plan->name]);

                // Fetch the full subscription details from Stripe
                $stripeSubscription = null;
                $trialEnd = null;

                if ($session->subscription) {
                    $stripeSubscription = StripeSubscription::retrieve($session->subscription);
                    \Log::info('Stripe subscription retrieved', ['subscription_id' => $session->subscription]);

                    // Extract trial end date if exists
                    if ($stripeSubscription->trial_end) {
                        $trialEnd = Carbon::createFromTimestamp($stripeSubscription->trial_end);
                        \Log::info('Trial end date found', ['trial_end' => $trialEnd->toDateString()]);
                    }
                }

                // Create subscription with trial info
                \Log::info('Creating subscription...', ['plan_id' => $plan->id, 'billing_period' => $metadata['billing_period']]);

                $subscription = $this->createSubscription(
                    $plan,
                    $metadata['billing_period'],
                    'stripe',
                    $session->subscription,
                    $trialEnd
                );

                \Log::info('Subscription created', ['subscription_id' => $subscription->id, 'user_id' => $subscription->user_id]);

                // Get transaction ID
                $transactionId = $session->payment_intent ?? $session->subscription ?? $session->id;

                // Create payment record only if actually paid
                if ($session->payment_status === 'paid') {
                    // Handle currency conversion - IDR doesn't use decimal places like USD
                    $currency = strtoupper($session->currency);
                    $amount = $currency === 'IDR' ? $session->amount_total : ($session->amount_total / 100);

                    Payment::create([
                        'user_id' => auth()->id(),
                        'user_subscription_id' => $subscription->id,
                        'transaction_id' => $transactionId,
                        'payment_gateway' => 'stripe',
                        'amount' => $amount,
                        'currency' => $currency,
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

                return redirect()->route('user.resumes.index')
                    ->with('success', $message);
            }

            return redirect()->route('user.pricing')
                ->with('error', 'Payment was not completed.');

        } catch (\Exception $e) {
            \Log::error('Error in stripeSuccess', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

        return redirect()->route('user.resumes.index')
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
        \Log::info('createSubscription called', [
            'plan_id' => $plan->id,
            'billing_period' => $billingPeriod,
            'gateway' => $gateway,
            'has_trial_end' => !is_null($trialEnd)
        ]);

        $user = auth()->user();

        \Log::info('Auth user', ['user_id' => $user?->id, 'user_email' => $user?->email]);

        if (!$user) {
            \Log::error('No authenticated user found');
            throw new \Exception('No authenticated user');
        }

        // Cancel existing active subscription
        $existingSubscription = $user->activeSubscription()->first();
        if ($existingSubscription) {
            \Log::info('Canceling existing subscription', ['existing_id' => $existingSubscription->id]);
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

        $data = [
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'billing_period' => $billingPeriod,
            'status' => 'active',
            'amount' => $plan->getPrice($billingPeriod),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'trial_end_date' => $trialEnd,
            'next_billing_date' => $trialEnd ?? $endDate,
            'auto_renew' => $autoRenew,
            'payment_gateway' => $gateway,
            'gateway_subscription_id' => $gatewaySubscriptionId,
        ];

        \Log::info('About to create subscription with data:', $data);

        try {
            $subscription = UserSubscription::create($data);
            \Log::info('Subscription created successfully', ['id' => $subscription->id, 'user_id' => $subscription->user_id]);
            return $subscription;
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error creating subscription', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error creating subscription', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Activate free plan without payment
     */
    private function activateFreePlan($plan)
    {
        $user = auth()->user();

        // Create free plan subscription (will cancel existing active one first)
        $subscription = $this->createSubscription($plan, 'monthly', null, null);

        return redirect()->route('user.subscription.dashboard')
            ->with('success', 'Free plan activated successfully!');
    }
}
