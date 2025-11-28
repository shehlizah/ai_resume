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
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        // For testing/development without proper signature
        if (!$endpoint_secret || !$sig_header) {
            \Log::info('Webhook received without signature (testing mode)', [
                'has_secret' => !!$endpoint_secret,
                'has_sig_header' => !!$sig_header
            ]);
            $event = json_decode($payload, true);
        } else {
            try {
                $event = Webhook::constructEvent(
                    $payload,
                    $sig_header,
                    $endpoint_secret
                );
            } catch (SignatureVerificationException $e) {
                \Log::error('Webhook signature verification failed', [
                    'error' => $e->getMessage(),
                    'has_secret' => !!$endpoint_secret,
                ]);
                // In development, still process the webhook
                if (app()->environment('local')) {
                    $event = json_decode($payload, true);
                } else {
                    return response()->json(['error' => 'Invalid signature'], 400);
                }
            }
        }

        // Handle the event
        if (isset($event['type'])) {
            match ($event['type']) {
                'customer.subscription.created' => $this->handleSubscriptionCreated($event['data']['object']),
                'customer.subscription.updated' => $this->handleSubscriptionUpdated($event['data']['object']),
                'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event['data']['object']),
                'charge.succeeded' => $this->handleChargeSucceeded($event['data']['object']),
                'charge.failed' => $this->handleChargeFailed($event['data']['object']),
                default => null,
            };
        } else {
            match ($event->type ?? null) {
                'customer.subscription.created' => $this->handleSubscriptionCreated($event->data->object),
                'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
                'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
                'charge.succeeded' => $this->handleChargeSucceeded($event->data->object),
                'charge.failed' => $this->handleChargeFailed($event->data->object),
                default => null,
            };
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle subscription created event
     */
    private function handleSubscriptionCreated($subscription)
    {
        try {
            // Get customer email to find user
            $customerId = $subscription['customer'] ?? $subscription->customer ?? null;

            if (!$customerId) {
                \Log::warning('No customer ID in subscription webhook');
                return;
            }

            // Try to get customer details from Stripe
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $customer = \Stripe\Customer::retrieve($customerId);
                $email = $customer->email;
            } catch (\Exception $e) {
                // If Stripe API fails (test mode), skip customer lookup
                \Log::warning('Could not retrieve Stripe customer: ' . $e->getMessage());
                return;
            }

            // Find user by email
            $user = User::where('email', $email)->first();
            if (!$user) {
                \Log::warning('User not found for email: ' . $email);
                return;
            }

            // Get the price from the subscription
            $items = $subscription['items']['data'] ?? $subscription->items->data ?? [];
            if (empty($items)) {
                \Log::warning('No items in subscription');
                return;
            }

            $priceData = $items[0]['price'] ?? $items[0]->price ?? null;
            if (!$priceData) {
                \Log::warning('No price data found');
                return;
            }

            $priceId = $priceData['id'] ?? $priceData->id ?? null;
            $unitAmount = $priceData['unit_amount'] ?? $priceData->unit_amount ?? 0;
            $interval = $priceData['recurring']['interval'] ?? $priceData->recurring->interval ?? 'month';

            // Find the plan by Stripe price ID
            $plan = SubscriptionPlan::where('stripe_price_id', $priceId)->first();

            if (!$plan) {
                \Log::warning('Plan not found for Stripe price: ' . $priceId);
                // For testing, create a basic plan or just log
                return;
            }

            // Get billing period - determine based on the plan
            $billingPeriod = 'monthly'; // Default
            if (str_contains($plan->slug, '6-month')) {
                $billingPeriod = 'semi-annual';
                $endDate = now()->addMonths(6);
            } elseif (str_contains($plan->slug, 'yearly')) {
                $billingPeriod = 'yearly';
                $endDate = now()->addYears(1);
            } else {
                $endDate = now()->addMonths(1);
            }

            // Extract trial end date from subscription
            $trialEnd = null;
            $stripeTrialEnd = $subscription['trial_end'] ?? $subscription->trial_end ?? null;
            if ($stripeTrialEnd) {
                $trialEnd = \Carbon\Carbon::createFromTimestamp($stripeTrialEnd);
            }

            // Create user subscription
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

            \Log::info('Subscription created for user ' . $user->id . ': ' . ($subscription['id'] ?? $subscription->id));
        } catch (\Exception $e) {
            \Log::error('Error in handleSubscriptionCreated: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
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
