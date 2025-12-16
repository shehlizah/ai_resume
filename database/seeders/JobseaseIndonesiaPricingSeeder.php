<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\AddOn;

class JobseaseIndonesiaPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder sets up the complete Jobsease pricing structure
     * with Indonesian Rupiah (IDR) pricing for user subscriptions.
     */
    public function run(): void
    {
        // Clear existing plans
        SubscriptionPlan::query()->delete();

        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for getting started with basic features',
                'monthly_price' => 0.00,
                'yearly_price' => 0.00,
                'stripe_price_id' => null,
                'stripe_monthly_price_id' => null,
                'stripe_yearly_price_id' => null,
                'template_limit' => 1, // 1 CV creation
                'access_premium_templates' => false,
                'priority_support' => false,
                'custom_branding' => false,
                'features' => [
                    '1 CV creation (basic template)',
                    'Basic CV sections',
                    'Basic interview questions (read only)',
                    'View 5 jobs',
                    'Apply to 1 job',
                    'Ads shown',
                ],
                'is_active' => true,
                'sort_order' => 1,
                'trial_days' => 0,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Everything you need for your career success',
                'monthly_price' => 49000.00, // IDR 49,000/month
                'yearly_price' => 399000.00, // IDR 399,000/year
                'stripe_price_id' => null, // To be updated with Stripe Price ID
                'stripe_monthly_price_id' => null,
                'stripe_yearly_price_id' => null,
                'template_limit' => null, // Unlimited
                'access_premium_templates' => true,
                'priority_support' => false,
                'custom_branding' => false,
                'features' => [
                    'Unlimited CVs',
                    'Premium templates',
                    'AI CV improvement',
                    'Resume score + suggestions',
                    'Unlimited job viewing',
                    'Unlimited job apply',
                    'AI interview practice',
                    'Interview score & feedback',
                    'No ads',
                ],
                'is_active' => true,
                'sort_order' => 2,
                'trial_days' => 0,
            ],
            [
                'name' => 'Career Pro+',
                'slug' => 'pro-plus',
                'description' => 'Advanced features for serious career professionals',
                'monthly_price' => 99000.00, // IDR 99,000/month
                'yearly_price' => 699000.00, // IDR 699,000/year
                'stripe_price_id' => null, // To be updated with Stripe Price ID
                'stripe_monthly_price_id' => null,
                'stripe_yearly_price_id' => null,
                'template_limit' => null, // Unlimited
                'access_premium_templates' => true,
                'priority_support' => true,
                'custom_branding' => true,
                'features' => [
                    'Everything in Pro',
                    'Priority job matching',
                    'Advanced interview questions (role-based)',
                    'Mock interview simulation',
                    'Discounts on human interview sessions',
                    'Priority support',
                    'Custom branding',
                ],
                'is_active' => true,
                'sort_order' => 3,
                'trial_days' => 0,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }

        // Create Human Interview Add-ons
        AddOn::query()->where('type', 'interview_session')->delete();

        $interviewAddOns = [
            [
                'name' => '30-Minute Interview Session',
                'slug' => '30-min-interview',
                'description' => 'One-on-one professional interview coaching session',
                'price' => 200000.00, // IDR 200,000 (middle of 150K-250K range)
                'type' => 'interview_session',
                'is_active' => true,
                'features' => [
                    '30 minutes with expert interviewer',
                    'Personalized feedback',
                    'Recording available',
                    'Written report',
                ],
                'content' => [
                    'duration_minutes' => 30,
                    'includes_recording' => true,
                    'includes_report' => true,
                ],
                'sort_order' => 1,
                'icon' => 'clock',
            ],
            [
                'name' => '60-Minute Interview Session',
                'slug' => '60-min-interview',
                'description' => 'Extended professional interview coaching session',
                'price' => 400000.00, // IDR 400,000 (middle of 300K-500K range)
                'type' => 'interview_session',
                'is_active' => true,
                'features' => [
                    '60 minutes with expert interviewer',
                    'In-depth personalized feedback',
                    'Recording available',
                    'Detailed written report',
                    'Follow-up email support',
                ],
                'content' => [
                    'duration_minutes' => 60,
                    'includes_recording' => true,
                    'includes_report' => true,
                    'includes_followup' => true,
                ],
                'sort_order' => 2,
                'icon' => 'clock',
            ],
        ];

        foreach ($interviewAddOns as $addOn) {
            AddOn::create($addOn);
        }

        $this->command->info('✅ Jobsease Indonesia pricing structure seeded successfully!');
        $this->command->info('📋 Created plans: Free, Pro (IDR 49K), Career Pro+ (IDR 99K)');
        $this->command->info('🎤 Created interview add-ons: 30-min (IDR 200K), 60-min (IDR 400K)');
    }
}
