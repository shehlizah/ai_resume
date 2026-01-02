<?php

namespace App\Jobs;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Artisan;
use function now;

class SendAbandonedCartReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        echo "[JOB] Starting\n";
        // Clear cache to avoid stale DB reads
        try {
            Artisan::call('cache:clear');
            echo "[JOB] Cache cleared\n";
        } catch (\Throwable $e) {
            echo "[WARN] Could not clear cache: " . $e->getMessage() . "\n";
        }

        try {
            $carts = AbandonedCart::where('status', 'abandoned')
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

                // If cart has no user and is signup type, send direct email
                if (!$cart->user && $cart->type === 'signup') {
                    $email = $cart->session_data['email'] ?? null;
                    if ($email) {
                        try {
                            $reminder = new \App\Notifications\IncompleteSignupReminder($cart);
                            $recoveryCount = $cart->recovery_email_sent_count + 1;
                            $mailMessage = $reminder->buildMailMessage('there', $recoveryCount);

                            // Send as raw HTML email - cast render() output to string
                            \Mail::raw((string) $mailMessage->render(), function ($message) use ($email, $mailMessage) {
                                $message->to($email)->subject($mailMessage->subject);
                            });

                            echo "[MAIL] IncompleteSignupReminder sent directly to $email\n";
                            $cart->markRecoveryEmailSent();
                            echo "[JOB] Cart #{$cart->id} marked as sent\n";
                        } catch (\Throwable $e) {
                            echo "[ERROR] Failed to send direct email to {$email}: " . $e->getMessage() . "\n";
                        }
                    } else {
                        echo "[SKIP] Cart #{$cart->id} has no user and no email in session_data.\n";
                    }
                    continue;
                }

                // If cart has no user and is pdf_preview type, send direct email
                if (!$cart->user && $cart->type === 'pdf_preview') {
                    $email = $cart->session_data['email'] ?? null;
                    if ($email) {
                        try {
                            $reminder = new \App\Notifications\PdfPreviewUpgradeReminder($cart);
                            $recoveryCount = $cart->recovery_email_sent_count + 1;
                            $mailMessage = $reminder->buildMailMessage('there', $recoveryCount);

                            // Send as raw HTML email - cast render() output to string
                            \Mail::raw((string) $mailMessage->render(), function ($message) use ($email, $mailMessage) {
                                $message->to($email)->subject($mailMessage->subject);
                            });

                            echo "[MAIL] PdfPreviewUpgradeReminder sent directly to $email\n";
                            $cart->markRecoveryEmailSent();
                            echo "[JOB] Cart #{$cart->id} marked as sent\n";
                        } catch (\Throwable $e) {
                            echo "[ERROR] Failed to send direct email to {$email}: " . $e->getMessage() . "\n";
                        }
                    } else {
                        echo "[SKIP] Cart #{$cart->id} has no user and no email in session_data.\n";
                    }
                    continue;
                }

                if (!$cart->user) {
                    echo "[SKIP] Cart #{$cart->id} has no user (user_id: {$cart->user_id}). Type: {$cart->type}, Session data: ".json_encode($cart->session_data)."\n";
                    continue;
                }

                try {
                    if ($cart->type === 'pdf_preview') {
                        $cart->user->notify(new \App\Notifications\PdfPreviewUpgradeReminder($cart));
                        echo "[NOTIFY] PdfPreviewUpgradeReminder sent to {$cart->user->email}\n";
                    } elseif ($cart->type === 'signup') {
                        $cart->user->notify(new \App\Notifications\IncompleteSignupReminder($cart));
                        echo "[NOTIFY] IncompleteSignupReminder sent to {$cart->user->email}\n";
                    } elseif ($cart->type === 'resume') {
                        $cart->user->notify(new \App\Notifications\IncompleteResumeReminder($cart));
                        echo "[NOTIFY] IncompleteResumeReminder sent to {$cart->user->email}\n";
                    } else {
                        echo "[SKIP] Cart #{$cart->id} has unknown type: {$cart->type}\n";
                    }
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
