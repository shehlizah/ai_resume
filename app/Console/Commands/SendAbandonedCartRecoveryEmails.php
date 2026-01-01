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
        $this->info('Checking for abandoned carts that need recovery emails...');

        $pendingCarts = AbandonedCart::getPendingRecovery();

        if ($pendingCarts->isEmpty()) {
            $this->info('No abandoned carts need recovery emails at this time.');
            return 0;
        }

        $sent = 0;
        foreach ($pendingCarts as $cart) {
            if (!$cart->user) {
                $this->warn("Skipping cart #{$cart->id}: No user associated");
                continue;
            }

            try {
                // Send appropriate notification based on type
                switch ($cart->type) {
                    case 'signup':
                        $cart->user->notify(new IncompleteSignupReminder($cart));
                        break;
                    case 'resume':
                        $cart->user->notify(new \App\Notifications\IncompleteResumeReminder($cart));
                        break;
                    case 'pdf_preview':
                    case 'payment':
                        $cart->user->notify(new PaymentAbandonedReminder($cart));
                        break;
                    default:
                        $this->warn("Unknown cart type: {$cart->type}");
                        continue 2;
                }

                $cart->markRecoveryEmailSent();
                $sent++;
                
                $emailNumber = $cart->recovery_email_sent_count;
                $this->info("Sent recovery email #{$emailNumber} to {$cart->user->email} (Cart #{$cart->id})");
            } catch (\Exception $e) {
                $this->error("Failed to send email for cart #{$cart->id}: " . $e->getMessage());
            }
        }

        $this->info("Completed! Sent {$sent} recovery emails.");
        return 0;
    }
}
