<?php

/**
 * Stripe Webhook Test & Debug Script
 *
 * Run this with: php artisan tinker
 * Then: include 'test-stripe-webhook.php';
 */

echo "=== STRIPE CONFIGURATION TEST ===\n\n";

// 1. Check environment variables
echo "1. Environment Variables:\n";
echo "   STRIPE_KEY: " . (config('services.stripe.key') ? '✅ Set' : '❌ Missing') . "\n";
echo "   STRIPE_SECRET: " . (config('services.stripe.secret') ? '✅ Set' : '❌ Missing') . "\n";
echo "   STRIPE_WEBHOOK_SECRET: " . (config('services.stripe.webhook.secret') ? '✅ Set' : '❌ Missing') . "\n";
echo "   APP_URL: " . config('app.url') . "\n\n";

// 2. Check database tables
echo "2. Database Tables:\n";
try {
    $usersCount = DB::table('users')->count();
    echo "   ✅ users table exists ({$usersCount} records)\n";
} catch (\Exception $e) {
    echo "   ❌ users table: " . $e->getMessage() . "\n";
}

try {
    $plansCount = DB::table('subscription_plans')->count();
    echo "   ✅ subscription_plans table exists ({$plansCount} records)\n";
} catch (\Exception $e) {
    echo "   ❌ subscription_plans table: " . $e->getMessage() . "\n";
}

try {
    $subsCount = DB::table('user_subscriptions')->count();
    echo "   ✅ user_subscriptions table exists ({$subsCount} records)\n";
} catch (\Exception $e) {
    echo "   ❌ user_subscriptions table: " . $e->getMessage() . "\n";
}

try {
    $paymentsCount = DB::table('payments')->count();
    echo "   ✅ payments table exists ({$paymentsCount} records)\n";
} catch (\Exception $e) {
    echo "   ❌ payments table: " . $e->getMessage() . "\n";
}

echo "\n3. Subscription Plans Configuration:\n";
$plans = \App\Models\SubscriptionPlan::all();
foreach ($plans as $plan) {
    echo "   Plan: {$plan->name} ({$plan->billing_period})\n";
    echo "      - ID: {$plan->id}\n";
    echo "      - Amount: \${$plan->amount}\n";
    echo "      - Stripe Price ID: " . ($plan->stripe_price_id ?: '❌ NOT SET') . "\n";
    echo "      - Active: " . ($plan->is_active ? 'Yes' : 'No') . "\n\n";
}

if ($plans->whereNull('stripe_price_id')->count() > 0) {
    echo "   ⚠️  WARNING: Some plans don't have Stripe Price IDs!\n";
    echo "   You need to create products in Stripe and update the database.\n\n";
}

// 4. Check webhook route
echo "4. Webhook Route:\n";
$routes = app('router')->getRoutes();
$webhookRoute = null;
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'webhooks/stripe')) {
        $webhookRoute = $route;
        break;
    }
}
if ($webhookRoute) {
    echo "   ✅ Webhook route registered: POST /" . $webhookRoute->uri() . "\n";
    echo "   Full URL: " . url($webhookRoute->uri()) . "\n\n";
} else {
    echo "   ❌ Webhook route not found!\n\n";
}

// 5. Check recent logs
echo "5. Recent Webhook Logs (last 10 lines):\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $recentLines = array_slice($lines, -10);
    foreach ($recentLines as $line) {
        if (str_contains($line, 'WEBHOOK') || str_contains($line, 'STRIPE') || str_contains($line, 'SUBSCRIPTION')) {
            echo "   " . trim($line) . "\n";
        }
    }
} else {
    echo "   ❌ Log file not found\n";
}

echo "\n=== SETUP CHECKLIST ===\n\n";
echo "[ ] 1. Create products in Stripe Dashboard (https://dashboard.stripe.com/test/products)\n";
echo "[ ] 2. Copy Price IDs from Stripe\n";
echo "[ ] 3. Update database with Price IDs:\n";
echo "        UPDATE subscription_plans SET stripe_price_id = 'price_xxx' WHERE id = X;\n";
echo "[ ] 4. Set up webhook in Stripe Dashboard:\n";
echo "        URL: " . url('/webhooks/stripe') . "\n";
echo "        Events: checkout.session.completed, customer.subscription.*, invoice.payment_*\n";
echo "[ ] 5. Copy Webhook Secret and add to .env:\n";
echo "        STRIPE_WEBHOOK_SECRET=whsec_xxx\n";
echo "[ ] 6. Run: php artisan config:clear\n";
echo "[ ] 7. Test payment with card: 4242 4242 4242 4242\n\n";

echo "=== QUICK FIX COMMANDS ===\n\n";
echo "# Update a plan's Stripe Price ID:\n";
echo "DB::table('subscription_plans')->where('id', 1)->update(['stripe_price_id' => 'price_xxxxx']);\n\n";
echo "# Check webhook logs:\n";
echo "tail -f storage/logs/laravel.log | grep WEBHOOK\n\n";
echo "# Test webhook manually:\n";
echo "curl -X POST " . url('/webhooks/stripe') . " -H 'Content-Type: application/json' -d '{}'\n\n";

echo "=== DONE ===\n";
