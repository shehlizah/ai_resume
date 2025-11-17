<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class UpdateStripepriceIds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * INSTRUCTIONS:
     * 1. Get your Stripe Price IDs from https://dashboard.stripe.com/products
     * 2. Update the price IDs below with your actual Stripe IDs
     * 3. Run: php artisan db:seed --class=UpdateStripepriceIds
     */
    public function run(): void
    {
        // Update Free Plan (usually doesn't have Stripe prices, but add if needed)
        SubscriptionPlan::where('slug', 'free')->update([
            'stripe_monthly_price_id' => null, // Free plan - no price ID needed
            'stripe_yearly_price_id' => null,
        ]);

        // Update Basic Plan
        // TODO: Replace with your actual Stripe Price IDs from dashboard
        SubscriptionPlan::where('slug', 'basic')->update([
            'stripe_monthly_price_id' => 'price_REPLACE_WITH_BASIC_MONTHLY', // e.g., price_1234567890abcdef
            'stripe_yearly_price_id' => 'price_REPLACE_WITH_BASIC_YEARLY',
        ]);

        // Update Premium Plan
        // TODO: Replace with your actual Stripe Price IDs from dashboard
        SubscriptionPlan::where('slug', 'premium')->update([
            'stripe_monthly_price_id' => 'price_REPLACE_WITH_PREMIUM_MONTHLY',
            'stripe_yearly_price_id' => 'price_REPLACE_WITH_PREMIUM_YEARLY',
        ]);

        echo "Stripe Price IDs updated!\n";
    }
}
