<?php

/**
 * Test Stripe Configuration
 * Run with: php artisan tinker
 * Then: include 'test-config.php';
 */

echo "=== STRIPE CONFIG TEST ===\n\n";

$stripeKey = config('services.stripe.key');
$stripeSecret = config('services.stripe.secret');
$webhookSecret = config('services.stripe.webhook_secret');

echo "STRIPE_KEY:\n";
echo "  Value: " . ($stripeKey ? substr($stripeKey, 0, 20) . '...' : '❌ MISSING') . "\n";
echo "  Length: " . strlen($stripeKey) . " chars\n\n";

echo "STRIPE_SECRET:\n";
echo "  Value: " . ($stripeSecret ? substr($stripeSecret, 0, 20) . '...' : '❌ MISSING') . "\n";
echo "  Length: " . strlen($stripeSecret) . " chars\n\n";

echo "STRIPE_WEBHOOK_SECRET:\n";
if ($webhookSecret) {
    echo "  ✅ SET\n";
    echo "  Value: " . substr($webhookSecret, 0, 20) . "...\n";
    echo "  Length: " . strlen($webhookSecret) . " chars\n";

    // Check for leading/trailing spaces
    $trimmed = trim($webhookSecret);
    if ($webhookSecret !== $trimmed) {
        echo "  ⚠️  WARNING: Has leading or trailing spaces!\n";
        echo "  Original: '" . $webhookSecret . "'\n";
        echo "  Trimmed:  '" . $trimmed . "'\n";
        echo "  Fix your .env file - remove spaces around the value!\n";
    } else {
        echo "  ✅ No extra spaces\n";
    }

    // Check format
    if (str_starts_with($webhookSecret, 'whsec_')) {
        echo "  ✅ Correct format (starts with 'whsec_')\n";
    } else {
        echo "  ❌ Wrong format (should start with 'whsec_')\n";
    }
} else {
    echo "  ❌ MISSING or empty\n";
    echo "  Add to .env: STRIPE_WEBHOOK_SECRET=whsec_your_secret\n";
}

echo "\n=== .ENV FILE CHECK ===\n";
$envFile = base_path('.env');
if (file_exists($envFile)) {
    echo "✅ .env file exists\n";
    $contents = file_get_contents($envFile);
    if (str_contains($contents, 'STRIPE_WEBHOOK_SECRET')) {
        echo "✅ STRIPE_WEBHOOK_SECRET found in .env\n";
        // Extract the line
        preg_match('/STRIPE_WEBHOOK_SECRET=(.*)/', $contents, $matches);
        if ($matches) {
            $envValue = trim($matches[1]);
            echo "Raw value from .env: '" . $envValue . "'\n";
            if ($envValue !== trim($envValue)) {
                echo "⚠️  FIX NEEDED: Remove spaces in .env file!\n";
                echo "Change from: STRIPE_WEBHOOK_SECRET= " . $envValue . "\n";
                echo "Change to:   STRIPE_WEBHOOK_SECRET=" . trim($envValue) . "\n";
            }
        }
    } else {
        echo "❌ STRIPE_WEBHOOK_SECRET not found in .env\n";
    }
} else {
    echo "❌ .env file not found!\n";
}

echo "\n=== DONE ===\n";
