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
            'customer.subscription.created' => $this->handleSubscriptionCreated($event['data']['object']),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event['data']['object']),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event['data']['object']),
            'charge.succeeded' => $this->handleChargeSucceeded($event['data']['object']),
            'charge.failed' => $this->handleChargeFailed($event['data']['object']),
            'invoice.payment_succeeded' => $this->handleChargeSucceeded($event['data']['object']), // Add this
            default => \Log::info('Unhandled event type: ' . $event['type']),
        };
    }

    return response()->json(['status' => 'success']);
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
        \Log::info('Price ID: ' . $priceId);

        // Find the plan by Stripe price ID
        $plan = SubscriptionPlan::where('stripe_price_id', $priceId)->first();

        if (!$plan) {
            \Log::error('Plan not found for Stripe price: ' . $priceId);
            \Log::info('Available plans in DB: ' . SubscriptionPlan::pluck('stripe_price_id')->toJson());
            return;
        }
        \Log::info('Plan found: ' . $plan->id . ' - ' . $plan->name);

        // ... rest of your subscription creation code
        
        $userSubscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'billing_period' => $billingPeriod,
            'status' => 'active',
            'amount' => $unitAmount / 100,
            'start_date' => now(),
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
