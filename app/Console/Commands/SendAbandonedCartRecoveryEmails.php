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
        
        \Log::info('=== SendAbandonedCartRecoveryEmails Command Started ===');
        $this->info('Checking for abandoned carts that need recovery emails...');

        echo "Getting pending carts...\n";
        $pendingCarts = AbandonedCart::getPendingRecovery();
        
        echo "Found " . count($pendingCarts) . " pending carts\n";
        \Log::info("Found " . count($pendingCarts) . " pending carts");
        $this->info("Found " . count($pendingCarts) . " pending carts");

        if ($pendingCarts->isEmpty()) {
            echo "No pending carts\n";
            \Log::info('No abandoned carts need recovery emails');
            $this->info('No abandoned carts need recovery emails at this time.');
            return 0;
        }

        echo "Processing " . count($pendingCarts) . " carts...\n";
        $sent = 0;
        foreach ($pendingCarts as $cart) {
            echo "Processing cart #{$cart->id}\n";

            try {
                \Log::info("Notifying user {$cart->user->email} for cart #{$cart->id}");

                // Send appropriate notification based on type
                switch ($cart->type) {
                    case 'signup':
                        \Log::info("Sending IncompleteSignupReminder for cart #{$cart->id}");
                        $cart->user->notify(new IncompleteSignupReminder($cart));
                        break;
                    case 'resume':
                        \Log::info("Sending IncompleteResumeReminder for cart #{$cart->id}");
                        $cart->user->notify(new \App\Notifications\IncompleteResumeReminder($cart));
                        break;
                    case 'pdf_preview':
                    case 'payment':
                        \Log::info("Sending PaymentAbandonedReminder for cart #{$cart->id}");
                        $cart->user->notify(new PaymentAbandonedReminder($cart));
                        break;
                    default:
                        \Log::warn("Unknown cart type: {$cart->type}");
                        $this->warn("Unknown cart type: {$cart->type}");
                        continue 2;
                }

                $cart->markRecoveryEmailSent();
                $sent++;

                $emailNumber = $cart->recovery_email_sent_count;
                \Log::info("Sent recovery email #{$emailNumber} to {$cart->user->email} (Cart #{$cart->id})");
                $this->info("Sent recovery email #{$emailNumber} to {$cart->user->email} (Cart #{$cart->id})");
            } catch (\Exception $e) {
                \Log::error("Failed to send email for cart #{$cart->id}: " . $e->getMessage());
                \Log::error($e->getTraceAsString());
                $this->error("Failed to send email for cart #{$cart->id}: " . $e->getMessage());
            }
        }

        \Log::info("Command completed. Sent {$sent} recovery emails.");
        $this->info("Completed! Sent {$sent} recovery emails.");
        return 0;
    }
}
