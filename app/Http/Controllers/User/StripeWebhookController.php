<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhooks
     */
   public function handleWebhook(Request $request)
{
    \Log::info('=== WEBHOOK RECEIVED ===');
    \Log::info('Payload: ' . $request->getContent());

    Stripe::setApiKey(config('services.stripe.secret'));

    $payload = $request->getContent();
    $sig_header = $request->header('Stripe-Signature');
    $endpoint_secret = config('services.stripe.webhook_secret');

    // For testing/development without proper signature
    if (!$endpoint_secret || !$sig_header) {
        \Log::info('Webhook received without signature (testing mode)');
        $event = json_decode($payload, true);
    } else {
        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
            \Log::info('Webhook signature verified successfully');
        } catch (SignatureVerificationException $e) {
            \Log::error('Webhook signature verification failed: ' . $e->getMessage());
            if (app()->environment('local')) {
                $event = json_decode($payload, true);
            } else {
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }
    }

    // Log the event type
    $eventType = $event['type'] ?? $event->type ?? 'unknown';
    \Log::info('Event Type: ' . $eventType);

    // Handle the event
    if (isset($event['type'])) {
        match ($event['type']) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event['data']['object']),
            'customer.subscription.created' => $this->handleSubscriptionCreated($event['data']['object']),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event['data']['object']),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event['data']['object']),
            'charge.succeeded' => $this->handleChargeSucceeded($event['data']['object']),
            'charge.failed' => $this->handleChargeFailed($event['data']['object']),
            'invoice.payment_succeeded' => $this->handleInvoicePaymentSucceeded($event['data']['object']),
            'invoice.payment_failed' => $this->handleInvoicePaymentFailed($event['data']['object']),
            default => \Log::info('Unhandled event type: ' . $event['type']),
        };
    }

    return response()->json(['status' => 'success']);
}

    /**
     * Handle checkout session completed event (MOST IMPORTANT)
     */
    private function handleCheckoutSessionCompleted($session)
    {
        \Log::info('=== CHECKOUT SESSION COMPLETED ===');
        \Log::info('Session ID: ' . ($session['id'] ?? $session->id ?? 'unknown'));

        try {
            $metadata = $session['metadata'] ?? $session->metadata ?? [];
            $userId = $metadata['user_id'] ?? null;
            $planId = $metadata['plan_id'] ?? null;
            $billingPeriod = $metadata['billing_period'] ?? 'monthly';

            if (!$userId || !$planId) {
                \Log::error('Missing metadata in checkout session', ['metadata' => $metadata]);
                return;
            }

            $user = User::find($userId);
            $plan = SubscriptionPlan::find($planId);

            if (!$user || !$plan) {
                \Log::error('User or plan not found', ['user_id' => $userId, 'plan_id' => $planId]);
                return;
            }

            \Log::info('Processing checkout for user: ' . $user->email . ', plan: ' . $plan->name);

            // Get subscription ID from session
            $subscriptionId = $session['subscription'] ?? $session->subscription ?? null;

            if ($subscriptionId) {
                // Fetch full subscription details
                Stripe::setApiKey(config('services.stripe.secret'));
                $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);

                // Extract dates
                $startDate = \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start);
                $endDate = \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end);
                $trialEnd = $stripeSubscription->trial_end
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                    : null;

                // Cancel existing active subscription
                $existingSubscription = $user->activeSubscription()->first();
                if ($existingSubscription) {
                    \Log::info('Canceling existing subscription', ['existing_id' => $existingSubscription->id]);
                    $existingSubscription->update(['status' => 'canceled', 'end_date' => now()]);
                }

                // Create subscription
                $userSubscription = UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'billing_period' => $billingPeriod,
                    'status' => 'active',
                    'amount' => $plan->getPrice($billingPeriod),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'next_billing_date' => $trialEnd ?? $endDate,
                    'trial_end_date' => $trialEnd,
                    'auto_renew' => true,
                    'payment_gateway' => 'stripe',
                    'gateway_subscription_id' => $subscriptionId,
                ]);

                \Log::info('✅ SUBSCRIPTION CREATED FROM CHECKOUT - ID: ' . $userSubscription->id);

                // Update user's stripe customer ID
                if (isset($session['customer'])) {
                    $customerId = $session['customer'] ?? $session->customer;
                    $user->update(['stripe_customer_id' => $customerId]);
                    \Log::info('Updated user stripe_customer_id: ' . $customerId);
                }

                // Create payment record if paid
                if (($session['payment_status'] ?? $session->payment_status) === 'paid') {
                    $transactionId = $session['payment_intent'] ?? $session->payment_intent ?? $session['id'] ?? $session->id;
                    $amount = ($session['amount_total'] ?? $session->amount_total ?? 0) / 100;

                    Payment::create([
                        'user_id' => $user->id,
                        'user_subscription_id' => $userSubscription->id,
                        'transaction_id' => $transactionId,
                        'payment_gateway' => 'stripe',
                        'amount' => $amount,
                        'currency' => strtoupper($session['currency'] ?? $session->currency ?? 'usd'),
                        'status' => 'completed',
                        'payment_type' => 'subscription',
                        'description' => 'Subscription to ' . $plan->name . ' plan',
                        'metadata' => [
                            'session_id' => $session['id'] ?? $session->id,
                            'customer_id' => $session['customer'] ?? $session->customer ?? null,
                            'subscription_id' => $subscriptionId,
                        ],
                        'paid_at' => now(),
                    ]);

                    \Log::info('✅ PAYMENT RECORDED - Amount: $' . $amount);
                }
            }

        } catch (\Exception $e) {
            \Log::error('❌ ERROR in handleCheckoutSessionCompleted: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Handle invoice payment succeeded
     */
    private function handleInvoicePaymentSucceeded($invoice)
    {
        \Log::info('=== INVOICE PAYMENT SUCCEEDED ===');

        try {
            $subscriptionId = $invoice['subscription'] ?? $invoice->subscription ?? null;

            if (!$subscriptionId) {
                \Log::warning('No subscription in invoice');
                return;
            }

            $userSubscription = UserSubscription::where('gateway_subscription_id', $subscriptionId)->first();

            if ($userSubscription) {
                // Create payment record
                $chargeId = $invoice['charge'] ?? $invoice->charge ?? $invoice['id'] ?? $invoice->id;

                Payment::create([
                    'user_id' => $userSubscription->user_id,
                    'user_subscription_id' => $userSubscription->id,
                    'transaction_id' => $chargeId,
                    'payment_gateway' => 'stripe',
                    'amount' => ($invoice['amount_paid'] ?? $invoice->amount_paid ?? 0) / 100,
                    'currency' => strtoupper($invoice['currency'] ?? $invoice->currency ?? 'USD'),
                    'status' => 'completed',
                    'payment_type' => 'subscription',
                    'description' => 'Subscription renewal for ' . ($userSubscription->plan->name ?? 'Plan'),
                    'metadata' => [
                        'invoice_id' => $invoice['id'] ?? $invoice->id,
                        'subscription_id' => $subscriptionId,
                        'customer_id' => $invoice['customer'] ?? $invoice->customer ?? null,
                    ],
                    'paid_at' => now(),
                ]);

                \Log::info('✅ RENEWAL PAYMENT RECORDED for user ' . $userSubscription->user_id);
            }
        } catch (\Exception $e) {
            \Log::error('❌ ERROR in handleInvoicePaymentSucceeded: ' . $e->getMessage());
        }
    }

    /**
     * Handle invoice payment failed
     */
    private function handleInvoicePaymentFailed($invoice)
    {
        \Log::info('=== INVOICE PAYMENT FAILED ===');

        try {
            $subscriptionId = $invoice['subscription'] ?? $invoice->subscription ?? null;

            if (!$subscriptionId) {
                return;
            }

            $userSubscription = UserSubscription::where('gateway_subscription_id', $subscriptionId)->first();

            if ($userSubscription) {
                // Update subscription status
                $userSubscription->update(['status' => 'past_due']);

                // Create failed payment record
                Payment::create([
                    'user_id' => $userSubscription->user_id,
                    'user_subscription_id' => $userSubscription->id,
                    'transaction_id' => $invoice['id'] ?? $invoice->id,
                    'payment_gateway' => 'stripe',
                    'amount' => ($invoice['amount_due'] ?? $invoice->amount_due ?? 0) / 100,
                    'currency' => strtoupper($invoice['currency'] ?? $invoice->currency ?? 'USD'),
                    'status' => 'failed',
                    'payment_type' => 'subscription',
                    'description' => 'Failed renewal for ' . ($userSubscription->plan->name ?? 'Plan'),
                    'metadata' => [
                        'invoice_id' => $invoice['id'] ?? $invoice->id,
                        'subscription_id' => $subscriptionId,
                        'failure_reason' => 'Payment failed',
                    ],
                ]);

                \Log::error('❌ RENEWAL PAYMENT FAILED for user ' . $userSubscription->user_id);
            }
        } catch (\Exception $e) {
            \Log::error('❌ ERROR in handleInvoicePaymentFailed: ' . $e->getMessage());
        }
    }

    /**
     * Handle subscription created event
     */
    private function handleSubscriptionCreated($subscription)
    {
        \Log::info('=== HANDLE SUBSCRIPTION CREATED ===');

        try {
            $customerId = $subscription['customer'] ?? $subscription->customer ?? null;
            \Log::info('Customer ID: ' . $customerId);

            if (!$customerId) {
                \Log::warning('No customer ID in subscription webhook');
                return;
            }

            // Try to get customer details from Stripe
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $customer = \Stripe\Customer::retrieve($customerId);
                $email = $customer->email;
                \Log::info('Customer email: ' . $email);
            } catch (\Exception $e) {
                \Log::error('Could not retrieve Stripe customer: ' . $e->getMessage());
                return;
            }

            // Find user by email
            $user = User::where('email', $email)->first();
            if (!$user) {
                \Log::error('User not found for email: ' . $email);
                return;
            }
            \Log::info('User found: ' . $user->id);

            // Get the price from the subscription
            $items = $subscription['items']['data'] ?? $subscription->items->data ?? [];
            \Log::info('Subscription items count: ' . count($items));

            if (empty($items)) {
                \Log::error('No items in subscription');
                return;
            }

            $priceData = $items[0]['price'] ?? $items[0]->price ?? null;
            if (!$priceData) {
                \Log::error('No price data found');
                return;
            }

            $priceId = $priceData['id'] ?? $priceData->id ?? null;
            $unitAmount = $priceData['unit_amount'] ?? $priceData->unit_amount ?? 0;
            $interval = $priceData['recurring']['interval'] ?? $priceData->recurring->interval ?? 'month';

            \Log::info('Price ID: ' . $priceId);
            \Log::info('Unit Amount: ' . $unitAmount);
            \Log::info('Interval: ' . $interval);

            // Find the plan by Stripe price ID
            $plan = SubscriptionPlan::where('stripe_price_id', $priceId)->first();

            if (!$plan) {
                \Log::error('Plan not found for Stripe price: ' . $priceId);
                \Log::info('Available plans in DB: ' . SubscriptionPlan::pluck('stripe_price_id')->toJson());
                return;
            }
            \Log::info('Plan found: ' . $plan->id . ' - ' . $plan->name);

            // Get billing period from interval
            $billingPeriod = $interval === 'year' ? 'yearly' : 'monthly';

            // Calculate dates
            $startDate = isset($subscription['current_period_start'])
                ? \Carbon\Carbon::createFromTimestamp($subscription['current_period_start'])
                : now();

            $endDate = isset($subscription['current_period_end'])
                ? \Carbon\Carbon::createFromTimestamp($subscription['current_period_end'])
                : ($billingPeriod === 'yearly' ? now()->addYear() : now()->addMonth());

            // Check for trial
            $trialEnd = null;
            if (isset($subscription['trial_end']) && $subscription['trial_end']) {
                $trialEnd = \Carbon\Carbon::createFromTimestamp($subscription['trial_end']);
                \Log::info('Trial end date: ' . $trialEnd->toDateTimeString());
            }

            // Cancel existing active subscription
            $existingSubscription = $user->activeSubscription()->first();
            if ($existingSubscription) {
                \Log::info('Canceling existing subscription', ['existing_id' => $existingSubscription->id]);
                $existingSubscription->update(['status' => 'canceled', 'end_date' => now()]);
            }

            // Create new subscription
            $userSubscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'billing_period' => $billingPeriod,
                'status' => 'active',
                'amount' => $unitAmount / 100,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'next_billing_date' => $trialEnd ?? $endDate,
                'trial_end_date' => $trialEnd,
                'auto_renew' => true,
                'payment_gateway' => 'stripe',
                'gateway_subscription_id' => $subscription['id'] ?? $subscription->id,
            ]);

            \Log::info('✅ SUBSCRIPTION CREATED IN DB - ID: ' . $userSubscription->id);

        } catch (\Exception $e) {
            \Log::error('❌ ERROR in handleSubscriptionCreated: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Handle subscription updated event
     */
    private function handleSubscriptionUpdated($subscription)
    {
        try {
            $subId = $subscription['id'] ?? $subscription->id ?? null;
            $userSubscription = UserSubscription::where('gateway_subscription_id', $subId)->first();

            if (!$userSubscription) {
                \Log::warning('Subscription not found in DB: ' . $subId);
                return;
            }

            // Update status based on Stripe subscription status
            $stripeStatus = $subscription['status'] ?? $subscription->status ?? 'active';
            $status = $this->mapStripeStatus($stripeStatus);

            $cancelAtPeriodEnd = $subscription['cancel_at_period_end'] ?? $subscription->cancel_at_period_end ?? false;

            $userSubscription->update([
                'status' => $status,
                'auto_renew' => !$cancelAtPeriodEnd,
            ]);

            if ($subscription['canceled_at'] ?? $subscription->canceled_at ?? null) {
                $userSubscription->update([
                    'status' => 'canceled',
                    'end_date' => now(),
                ]);
            }

            \Log::info('Subscription updated: ' . $subId);
        } catch (\Exception $e) {
            \Log::error('Error in handleSubscriptionUpdated: ' . $e->getMessage());
        }
    }

    /**
     * Handle subscription deleted event
     */
    private function handleSubscriptionDeleted($subscription)
    {
        try {
            $subId = $subscription['id'] ?? $subscription->id ?? null;
            $userSubscription = UserSubscription::where('gateway_subscription_id', $subId)->first();

            if (!$userSubscription) {
                return;
            }

            $userSubscription->update([
                'status' => 'canceled',
                'end_date' => now(),
            ]);

            \Log::info('Subscription deleted: ' . $subId);
        } catch (\Exception $e) {
            \Log::error('Error in handleSubscriptionDeleted: ' . $e->getMessage());
        }
    }

    /**
     * Handle charge succeeded event
     */
    private function handleChargeSucceeded($charge)
    {
        try {
            $invoiceId = $charge['invoice'] ?? $charge->invoice ?? null;

            if (!$invoiceId) {
                \Log::warning('No invoice in charge');
                return;
            }

            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $invoice = \Stripe\Invoice::retrieve($invoiceId);
                $subscriptionId = $invoice->subscription;
            } catch (\Exception $e) {
                \Log::warning('Could not retrieve invoice: ' . $e->getMessage());
                return;
            }

            if (!$subscriptionId) {
                return;
            }

            $userSubscription = UserSubscription::where('gateway_subscription_id', $subscriptionId)->first();

            if ($userSubscription) {
                // Create payment record
                Payment::create([
                    'user_id' => $userSubscription->user_id,
                    'user_subscription_id' => $userSubscription->id,
                    'transaction_id' => $charge['id'] ?? $charge->id,
                    'payment_gateway' => 'stripe',
                    'amount' => ($charge['amount'] ?? $charge->amount ?? 0) / 100,
                    'currency' => strtoupper($charge['currency'] ?? $charge->currency ?? 'USD'),
                    'status' => 'completed',
                    'payment_type' => 'subscription',
                    'description' => 'Subscription payment for ' . ($userSubscription->plan->name ?? 'Plan'),
                    'metadata' => [
                        'charge_id' => $charge['id'] ?? $charge->id,
                        'invoice_id' => $invoiceId,
                        'subscription_id' => $subscriptionId,
                        'customer_id' => $charge['customer'] ?? $charge->customer ?? null,
                    ],
                    'paid_at' => now(),
                ]);

                \Log::info('Payment recorded for user ' . $userSubscription->user_id . ': ' . ($charge['id'] ?? $charge->id));
            }
        } catch (\Exception $e) {
            \Log::error('Error in handleChargeSucceeded: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }

    /**
     * Handle charge failed event
     */
    private function handleChargeFailed($charge)
    {
        try {
            $invoiceId = $charge['invoice'] ?? $charge->invoice ?? null;

            if (!$invoiceId) {
                return;
            }

            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $invoice = \Stripe\Invoice::retrieve($invoiceId);
                $subscriptionId = $invoice->subscription;
            } catch (\Exception $e) {
                \Log::warning('Could not retrieve invoice: ' . $e->getMessage());
                return;
            }

            if (!$subscriptionId) {
                return;
            }

            $userSubscription = UserSubscription::where('gateway_subscription_id', $subscriptionId)->first();

            if ($userSubscription) {
                // Create failed payment record
                Payment::create([
                    'user_id' => $userSubscription->user_id,
                    'user_subscription_id' => $userSubscription->id,
                    'transaction_id' => $charge['id'] ?? $charge->id,
                    'payment_gateway' => 'stripe',
                    'amount' => ($charge['amount'] ?? $charge->amount ?? 0) / 100,
                    'currency' => strtoupper($charge['currency'] ?? $charge->currency ?? 'USD'),
                    'status' => 'failed',
                    'payment_type' => 'subscription',
                    'description' => 'Failed payment for ' . ($userSubscription->plan->name ?? 'Plan'),
                    'metadata' => [
                        'charge_id' => $charge['id'] ?? $charge->id,
                        'invoice_id' => $invoiceId,
                        'subscription_id' => $subscriptionId,
                        'failure_reason' => $charge['failure_message'] ?? $charge->failure_message ?? 'Unknown',
                    ],
                ]);

                // Update subscription status
                $userSubscription->update(['status' => 'past_due']);

                \Log::error('Payment failed for user ' . $userSubscription->user_id);
            }
        } catch (\Exception $e) {
            \Log::error('Error in handleChargeFailed: ' . $e->getMessage());
        }
    }

    /**
     * Map Stripe subscription status to our status
     */
    private function mapStripeStatus($stripeStatus)
    {
        return match ($stripeStatus) {
            'active' => 'active',
            'past_due' => 'past_due',
            'canceled' => 'canceled',
            'unpaid' => 'unpaid',
            'incomplete' => 'pending',
            'incomplete_expired' => 'failed',
            default => 'unknown',
        };
    }
}
