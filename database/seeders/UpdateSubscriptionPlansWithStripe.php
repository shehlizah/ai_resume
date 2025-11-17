<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class UpdateSubscriptionPlansWithStripe extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder updates subscription plans with Stripe Price IDs
     * for the AI Resume pricing structure:
     * - Basic Monthly: $20/month
     * - 6-Month Plan: $108 for 6 months
     * - Yearly Plan: $200/year
     */
    public function run(): void
    {
        // Delete old plans and create new ones with correct pricing
        SubscriptionPlan::truncate();

        $plans = [
            [
                'name' => 'Basic Monthly',
                'slug' => 'basic-monthly',
                'description' => 'Perfect for getting started - $20 per month',
                'monthly_price' => 20.00,
                'yearly_price' => 0.00,
                'stripe_price_id' => 'price_1STdjrDfpo67wO4dMpPYTj9U',
                'template_limit' => 10,
                'access_premium_templates' => true,
                'priority_support' => false,
                'custom_branding' => false,
                'features' => [
                    'All basic templates',
                    'Create up to 10 resumes',
                    'PDF & Word download',
                    'Advanced editing tools',
                    'Cover letter templates',
                    'Email support',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '6-Month Plan',
                'slug' => '6-month',
                'description' => 'Great savings - $108 for 6 months ($18/month)',
                'monthly_price' => 18.00, // For display purposes
                'yearly_price' => 108.00,
                'stripe_price_id' => 'price_1STdknDfpo67wO4dM0Gz6azq',
                'template_limit' => null, // Unlimited
                'access_premium_templates' => true,
                'priority_support' => true,
                'custom_branding' => true,
                'features' => [
                    'All templates including premium',
                    'Unlimited resume creation',
                    'PDF, Word & HTML download',
                    'AI-powered content suggestions',
                    'Custom branding & watermark removal',
                    'Priority 24/7 support',
                    'LinkedIn profile optimization',
                    'Career coaching resources',
                    '6-month access',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Yearly Plan',
                'slug' => 'yearly',
                'description' => 'Best value - $200 per year ($16.67/month)',
                'monthly_price' => 16.67,
                'yearly_price' => 200.00,
                'stripe_price_id' => 'price_1STdknDfpo67wO4doA7beiRC',
                'template_limit' => null, // Unlimited
                'access_premium_templates' => true,
                'priority_support' => true,
                'custom_branding' => true,
                'features' => [
                    'All templates including premium',
                    'Unlimited resume creation',
                    'PDF, Word & HTML download',
                    'AI-powered content suggestions',
                    'Custom branding & watermark removal',
                    'Priority 24/7 support',
                    'LinkedIn profile optimization',
                    'Career coaching resources',
                    'Full year access',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }

        echo "✅ Subscription plans updated with Stripe Price IDs!\n";
        echo "Plans created:\n";
        echo "1. Basic Monthly - price_1STdjrDfpo67wO4dMpPYTj9U\n";
        echo "2. 6-Month Plan - price_1STdknDfpo67wO4dM0Gz6azq\n";
        echo "3. Yearly Plan - price_1STdknDfpo67wO4doA7beiRC\n";
    }
}
