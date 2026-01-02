<?php

namespace App\Jobs;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class SendAbandonedCartReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        echo "[JOB] Starting\n";
        // Clear cache to avoid stale DB reads
        try {
            \Artisan::call('cache:clear');
            echo "[JOB] Cache cleared\n";
        } catch (\Throwable $e) {
            echo "[WARN] Could not clear cache: " . $e->getMessage() . "\n";
        }

        try {
            $carts = AbandonedCart::where('status', 'abandoned')
                ->where('user_id', '!=', null)
                ->where('created_at', '<', now()->subHours(1))
                ->get();

            echo "[JOB] Found " . count($carts) . " carts\n";

            // Deduplicate: Only one email per user per cart type per timing
            $sent = [];
            foreach ($carts as $cart) {
                $key = $cart->user_id . '-' . $cart->type . '-' . $cart->recovery_email_sent_count;
                if (isset($sent[$key])) {
                    echo "[SKIP] Duplicate for user {$cart->user_id}, type {$cart->type}, timing {$cart->recovery_email_sent_count}\n";
                    continue;
                }
                $sent[$key] = true;

                echo "[JOB] Cart #{$cart->id}\n";

                if (!$cart->shouldSendRecoveryEmail()) {
                    echo "[SKIP] Cart #{$cart->id} not eligible for recovery email. Status: {$cart->status}, Recovery sent: {$cart->recovery_email_sent_count}, Created: {$cart->created_at}\n";
                    continue;
                }

                if (!$cart->user) {
                    echo "[SKIP] Cart #{$cart->id} has no user (user_id: {$cart->user_id}). Type: {$cart->type}, Session data: ".json_encode($cart->session_data)."\n";
                    continue;
                }

                try {
                    if ($cart->type === 'payment') {
                        $cart->user->notify(new \App\Notifications\PaymentAbandonedReminder($cart));
                        echo "[NOTIFY] PaymentAbandonedReminder sent to {$cart->user->email}\n";
                    }
                    // Add similar notification logic for signup and resume if needed
                    $cart->markRecoveryEmailSent();
                    echo "[JOB] Cart #{$cart->id} marked as sent\n";
                } catch (\Throwable $e) {
                    echo "[ERROR] Cart #{$cart->id}: " . $e->getMessage() . "\n";
                    echo "[TRACE] " . $e->getTraceAsString() . "\n";
                    file_put_contents('/tmp/abandoned-cart-error.log', date('Y-m-d H:i:s') . " Cart #{$cart->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n", FILE_APPEND);
                }
            }

            echo "[JOB] Done\n";
        } catch (\Throwable $e) {
            echo "[FATAL] " . $e->getMessage() . "\n";
            echo "[TRACE] " . $e->getTraceAsString() . "\n";
            file_put_contents('/tmp/abandoned-cart-error.log', date('Y-m-d H:i:s') . " FATAL: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n", FILE_APPEND);
            throw $e;
        }
    }

    // Removed: now using notifications only
  }
