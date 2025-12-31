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
        // Get all abandoned carts that need recovery emails
        $pendingCarts = AbandonedCart::getPendingRecovery();

        foreach ($pendingCarts as $cart) {
            if (!$cart->user) {
                continue;
            }

            // Send appropriate notification based on cart type
            match ($cart->type) {
                'signup' => $cart->user->notify(new IncompleteSignupReminder($cart)),
                'resume' => $cart->user->notify(new IncompleteResumeReminder($cart)),
                'pdf_preview' => $cart->user->notify(new PdfPreviewUpgradeReminder($cart)),
                default => null,
            };

            // Mark email as sent
            $cart->markRecoveryEmailSent();

            // Log the recovery email
            \Log::info("Abandoned cart recovery email sent", [
                'cart_id' => $cart->id,
                'user_id' => $cart->user_id,
                'type' => $cart->type,
                'email_count' => $cart->recovery_email_sent_count,
            ]);
        }
    }
}
