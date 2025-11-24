<?php
/**
 * Test Script: Direct Payment Creation
 *
 * This script creates a payment record directly in the database for testing.
 * Run from project root: php test-payment-creation.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test payment data
$paymentData = [
    'user_id' => 1, // Change to valid user ID
    'user_subscription_id' => 1, // Change to valid subscription ID or set to NULL
    'transaction_id' => 'test_txn_' . uniqid(),
    'payment_gateway' => 'stripe',
    'amount' => 9.99,
    'currency' => 'USD',
    'status' => 'completed',
    'payment_type' => 'subscription',
    'description' => 'Test payment creation',
    'metadata' => json_encode([
        'test' => true,
        'created_by' => 'test_script'
    ]),
    'paid_at' => now(),
    'created_at' => now(),
    'updated_at' => now(),
];

try {
    // Insert payment
    $paymentId = DB::table('payments')->insertGetId($paymentData);

    echo "✅ Payment created successfully!\n";
    echo "Payment ID: {$paymentId}\n";
    echo "Transaction ID: {$paymentData['transaction_id']}\n";
    echo "\nVerify in database:\n";
    echo "SELECT * FROM payments WHERE id = {$paymentId};\n";

} catch (\Exception $e) {
    echo "❌ Error creating payment:\n";
    echo $e->getMessage() . "\n";

    // Check if tables exist
    echo "\nChecking if payments table exists...\n";
    $tableExists = DB::select("SHOW TABLES LIKE 'payments'");
    if (empty($tableExists)) {
        echo "⚠️  Payments table does not exist! Run migrations:\n";
        echo "php artisan migrate\n";
    }
}
