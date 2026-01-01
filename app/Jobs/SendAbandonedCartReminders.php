<?php

namespace App\Jobs;

use App\Models\AbandonedCart;
use App\Notifications\IncompleteSignupReminder;
use App\Notifications\IncompleteResumeReminder;
use App\Notifications\PdfPreviewUpgradeReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAbandonedCartReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        echo "[JOB] Starting SendAbandonedCartReminders job\n";
        
        try {
            // Get all abandoned carts that need recovery emails
            $pendingCarts = AbandonedCart::getPendingRecovery();
            echo "[JOB] Found " . count($pendingCarts) . " pending carts\n";

            foreach ($pendingCarts as $cart) {
                echo "[JOB] Processing cart #{$cart->id}\n";
                
                if (!$cart->user) {
                    echo "[JOB] Cart #{$cart->id} has no user, skipping\n";
                    continue;
                }

                try {
                    // Send appropriate notification based on cart type
                    echo "[JOB] Sending {$cart->type} notification for cart #{$cart->id}\n";
                    
                    match ($cart->type) {
                        'signup' => $cart->user->notify(new IncompleteSignupReminder($cart)),
                        'resume' => $cart->user->notify(new IncompleteResumeReminder($cart)),
                        'pdf_preview' => $cart->user->notify(new PdfPreviewUpgradeReminder($cart)),
                        'payment' => $cart->user->notify(new \App\Notifications\PaymentAbandonedReminder($cart)),
                        default => null,
                    };

                    echo "[JOB] Notification sent successfully\n";
                    
                    // Mark email as sent
                    $cart->markRecoveryEmailSent();
                    echo "[JOB] Email marked as sent for cart #{$cart->id}\n";

                    // Log the recovery email
                    \Log::info("Abandoned cart recovery email sent", [
                        'cart_id' => $cart->id,
                        'user_id' => $cart->user_id,
                        'type' => $cart->type,
                        'email_count' => $cart->recovery_email_sent_count,
                    ]);
                } catch (\Exception $e) {
                    echo "[JOB ERROR] Failed to send notification for cart #{$cart->id}: " . $e->getMessage() . "\n";
                    echo $e->getTraceAsString() . "\n";
                    \Log::error("Failed to send notification for cart #{$cart->id}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
            
            echo "[JOB] Job completed successfully\n";
        } catch (\Exception $e) {
            echo "[JOB FATAL ERROR] " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
            \Log::error("SendAbandonedCartReminders job failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
