<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Notifications\IncompleteSignupReminder;
use App\Notifications\PaymentAbandonedReminder;
use Illuminate\Console\Command;

class SendAbandonedCartRecoveryEmails extends Command
{
    protected $signature = 'abandoned-carts:send-recovery-emails';
    protected $description = 'Send recovery emails to users with abandoned carts';

    public function handle()
    {
        echo "=== COMMAND STARTING ===\n";

        try {
            \Log::info('=== SendAbandonedCartRecoveryEmails Command Started ===');
            $this->info('Checking for abandoned carts that need recovery emails...');

            echo "Getting pending carts...\n";
            $pendingCarts = AbandonedCart::getPendingRecovery();

            echo "Found " . count($pendingCarts) . " pending carts\n";
            \Log::info("Found " . count($pendingCarts) . " pending carts");
            $this->info("Found " . count($pendingCarts) . " pending carts");
        } catch (\Exception $e) {
            echo "FATAL ERROR: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
            return 1;
        }

        if ($pendingCarts->isEmpty()) {
            echo "No pending carts\n";
            \Log::info('No abandoned carts need recovery emails');
            $this->info('No abandoned carts need recovery emails at this time.');
            return 0;
        }

        echo "Processing " . count($pendingCarts) . " carts...\n";
        $sent = 0;
        foreach ($pendingCarts as $cart) {
            echo "[SKIP] Notification system disabled. Use SendAbandonedCartReminders job only.\n";
        }

        \Log::info("Command completed. Sent {$sent} recovery emails.");
        $this->info("Completed! Sent {$sent} recovery emails.");
        return 0;
    }
}
