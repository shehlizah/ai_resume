<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for getting started with basic resume creation',
                'monthly_price' => 0.00,
                'yearly_price' => 0.00,
                'template_limit' => 3, // Can create max 3 resumes
                'access_premium_templates' => false,
                'priority_support' => false,
                'custom_branding' => false,
                'features' => [
                    'Access to 2-3 basic templates',
                    'Create up to 3 resumes',
                    'PDF download',
                    'Basic editing tools',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Ideal for job seekers who need more flexibility',
                'monthly_price' => 9.99,
                'yearly_price' => 99.99, // Save $20 per year
                'template_limit' => 10, // Can create max 10 resumes
                'access_premium_templates' => false,
                'priority_support' => false,
                'custom_branding' => false,
                'features' => [
                    'Access to all basic templates',
                    'Create up to 10 resumes',
                    'PDF & Word download',
                    'Advanced editing tools',
                    'Cover letter templates',
                    'Email support',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Everything you need for professional career advancement',
                'monthly_price' => 19.99,
                'yearly_price' => 199.99, // Save $40 per year
                'template_limit' => null, // Unlimited
                'access_premium_templates' => true,
                'priority_support' => true,
                'custom_branding' => true,
                'features' => [
                    'Access to ALL templates (including premium)',
                    'Unlimited resume creation',
                    'PDF, Word & HTML download',
                    'AI-powered content suggestions',
                    'Custom branding & watermark removal',
                    'Priority 24/7 support',
                    'LinkedIn profile optimization',
                    'Career coaching resources',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Subscription plans seeded successfully!');
    }
}