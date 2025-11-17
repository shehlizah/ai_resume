<?php
// tests/Feature/StripeWebhookTest.php

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;

class StripeWebhookTest extends TestCase
{
    public function test_subscription_created_webhook()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $plan = SubscriptionPlan::factory()->create([
            'stripe_monthly_price_id' => 'price_test_123'
        ]);

        $payload = [
            'type' => 'customer.subscription.created',
            'data' => [
                'object' => [
                    'id' => 'sub_test_123',
                    'customer' => 'cus_test_456',
                    'status' => 'active',
                    'items' => [
                        'data' => [
                            [
                                'price' => [
                                    'id' => 'price_test_123',
                                    'unit_amount' => 9999,
                                    'recurring' => ['interval' => 'month']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/webhooks/stripe', $payload, [
            'Stripe-Signature' => 'test_signature'
        ]);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'gateway_subscription_id' => 'sub_test_123'
        ]);
    }
}
