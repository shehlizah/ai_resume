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

        // For testing without proper signature, allow if STRIPE_WEBHOOK_SECRET is not set
        if (!$endpoint_secret) {
            $event = json_decode($payload, true);
        } else {
            try {
                $event = Webhook::constructEvent(
                    $payload,
                    $sig_header,
                    $endpoint_secret
                );
            } catch (SignatureVerificationException $e) {
                return response()->json(['error' => 'Invalid signature'], 400);
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
            match ($event->type) {
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
        // Get customer email to find user
        $customerId = $subscription->customer;

        // Get customer details from Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
        $customer = \Stripe\Customer::retrieve($customerId);

        // Find user by email
        $user = User::where('email', $customer->email)->first();
        if (!$user) {
            \Log::error('User not found for Stripe customer: ' . $customer->email);
            return;
        }

        // Get the price from the subscription
        $priceId = $subscription->items->data[0]->price->id;

        // Find the plan by Stripe price ID
        // You'll need to add stripe_price_id to SubscriptionPlan model
        $plan = SubscriptionPlan::where('stripe_price_id', $priceId)->first();

        if (!$plan) {
            \Log::error('Plan not found for Stripe price: ' . $priceId);
            return;
        }

        // Get billing period from price
        $price = $subscription->items->data[0]->price;
        $billingPeriod = $price->recurring->interval === 'year' ? 'yearly' : 'monthly';

        // Create user subscription
        $userSubscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'billing_period' => $billingPeriod,
            'status' => 'active',
            'amount' => $price->unit_amount / 100,
            'start_date' => now(),
            'end_date' => now()->addMonths($billingPeriod === 'yearly' ? 12 : 1),
            'next_billing_date' => now()->addMonths($billingPeriod === 'yearly' ? 12 : 1),
            'auto_renew' => true,
            'payment_gateway' => 'stripe',
            'gateway_subscription_id' => $subscription->id,
        ]);

        \Log::info('Subscription created for user ' . $user->id . ': ' . $subscription->id);
    }

    /**
     * Handle subscription updated event
     */
    private function handleSubscriptionUpdated($subscription)
    {
        $userSubscription = UserSubscription::where('gateway_subscription_id', $subscription->id)->first();

        if (!$userSubscription) {
            return;
        }

        // Update status based on Stripe subscription status
        $status = $this->mapStripeStatus($subscription->status);

        $userSubscription->update([
            'status' => $status,
            'auto_renew' => !$subscription->cancel_at_period_end,
        ]);

        if ($subscription->canceled_at) {
            $userSubscription->update([
                'status' => 'canceled',
                'end_date' => now(),
            ]);
        }

        \Log::info('Subscription updated: ' . $subscription->id);
    }

    /**
     * Handle subscription deleted event
     */
    private function handleSubscriptionDeleted($subscription)
    {
        $userSubscription = UserSubscription::where('gateway_subscription_id', $subscription->id)->first();

        if (!$userSubscription) {
            return;
        }

        $userSubscription->update([
            'status' => 'canceled',
            'end_date' => now(),
        ]);

        \Log::info('Subscription deleted: ' . $subscription->id);
    }

    /**
     * Handle charge succeeded event
     */
    private function handleChargeSucceeded($charge)
    {
        if ($charge->invoice) {
            Stripe::setApiKey(config('services.stripe.secret'));
            $invoice = \Stripe\Invoice::retrieve($charge->invoice);

            if ($invoice->subscription) {
                $userSubscription = UserSubscription::where('gateway_subscription_id', $invoice->subscription)->first();

                if ($userSubscription) {
                    // Create payment record
                    Payment::create([
                        'user_id' => $userSubscription->user_id,
                        'user_subscription_id' => $userSubscription->id,
                        'transaction_id' => $charge->id,
                        'payment_gateway' => 'stripe',
                        'amount' => $charge->amount / 100,
                        'currency' => strtoupper($charge->currency),
                        'status' => 'completed',
                        'payment_type' => 'subscription',
                        'description' => 'Subscription payment for ' . $userSubscription->plan->name,
                        'metadata' => [
                            'charge_id' => $charge->id,
                            'invoice_id' => $charge->invoice,
                            'subscription_id' => $invoice->subscription,
                            'customer_id' => $charge->customer,
                        ],
                        'paid_at' => now(),
                    ]);

                    \Log::info('Payment recorded for user ' . $userSubscription->user_id . ': ' . $charge->id);
                }
            }
        }
    }

    /**
     * Handle charge failed event
     */
    private function handleChargeFailed($charge)
    {
        if ($charge->invoice) {
            Stripe::setApiKey(config('services.stripe.secret'));
            $invoice = \Stripe\Invoice::retrieve($charge->invoice);

            if ($invoice->subscription) {
                $userSubscription = UserSubscription::where('gateway_subscription_id', $invoice->subscription)->first();

                if ($userSubscription) {
                    // Create failed payment record
                    Payment::create([
                        'user_id' => $userSubscription->user_id,
                        'user_subscription_id' => $userSubscription->id,
                        'transaction_id' => $charge->id,
                        'payment_gateway' => 'stripe',
                        'amount' => $charge->amount / 100,
                        'currency' => strtoupper($charge->currency),
                        'status' => 'failed',
                        'payment_type' => 'subscription',
                        'description' => 'Failed payment for ' . $userSubscription->plan->name,
                        'metadata' => [
                            'charge_id' => $charge->id,
                            'invoice_id' => $charge->invoice,
                            'subscription_id' => $invoice->subscription,
                            'failure_reason' => $charge->failure_message ?? 'Unknown',
                        ],
                    ]);

                    // Update subscription status
                    $userSubscription->update(['status' => 'past_due']);

                    \Log::error('Payment failed for user ' . $userSubscription->user_id . ': ' . $charge->failure_message);
                }
            }
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
