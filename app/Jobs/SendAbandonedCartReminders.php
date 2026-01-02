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

        try {
            $carts = AbandonedCart::where('status', 'abandoned')
                ->where('user_id', '!=', null)
                ->where('created_at', '<', now()->subHours(1))
                ->get();

            echo "[JOB] Found " . count($carts) . " carts\n";

            foreach ($carts as $cart) {
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
                    $this->sendEmail($cart);
                    $cart->markRecoveryEmailSent();
                    echo "[JOB] Cart #{$cart->id} sent\n";
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

    private function sendEmail(AbandonedCart $cart)
    {
        $emailNum = $cart->recovery_email_sent_count + 1;
        $user = $cart->user;
        $subject = '';
        $body = '';

        if ($cart->type === 'payment') {
            $url = route('pricing');
            if ($cart->session_data['resume_id'] ?? null) {
                $url .= '?resume=' . $cart->session_data['resume_id'];
            }

            if ($emailNum === 1) {
                $subject = 'Complete Your Jobsease Upgrade';
                $body = "Hi,\n\nWe noticed you were just one step away from upgrading your Jobsease account, but the checkout wasn't completed.\n\nYour selected plan is still reserved and gives you access to:\n• Faster job matching\n• Priority visibility to employers\n• Advanced profile & application features\n\nYou can continue anytime from where you left off — no need to start again.\n\nComplete your upgrade: " . $url . "\n\nIf you faced any issue during checkout, feel free to reply to this email. We're happy to help.\n\nBest regards,\nJobsease Team";
            } elseif ($emailNum === 2) {
                $subject = 'Your Jobsease Upgrade is Still Pending';
                $body = "Hi,\n\nJust a quick reminder about your pending Jobsease upgrade.\n\nUpgrading your account helps you:\n• Get noticed by employers faster\n• Apply to premium job listings\n• Improve your chances with enhanced profile tools\n\nMany candidates using upgraded plans get responses significantly quicker than free users.\n\nYour upgrade is still pending — you can activate it in just a minute.\n\nUpgrade now: " . $url . "\n\nIf you have questions about plans or pricing, simply reply to this email.\n\nRegards,\nJobsease Team";
            } else {
                $subject = 'Final Reminder: Your Jobsease Upgrade';
                $body = "Hi,\n\nThis is a final reminder regarding your incomplete Jobsease upgrade.\n\nYour selected plan may expire soon, and you could miss out on:\n• Priority employer access\n• Advanced job application tools\n• Better visibility in searches\n\nIf you're still looking to move ahead in your career, now is the best time to upgrade.\n\nComplete your upgrade: " . $url . "\n\nNeed help or unsure which plan is right for you? Just reply — our team is here.\n\nBest wishes,\nJobsease Team";
            }
        } elseif ($cart->type === 'signup') {
            $subject = 'Complete Your Account Setup';
            $body = "Hi " . $user->name . ",\n\n" . ($emailNum === 1 ? "You started creating your account but didn't complete it. Setting a password only takes 30 seconds!" : "We noticed your account is still incomplete. Your password is the final step!") . "\n\nBenefits of completing your signup:\n✓ Access to resume builder\n✓ Job matching and alerts\n✓ Interview preparation tools\n✓ Premium templates\n\nComplete your account setup: " . route('password.request') . "\n\nIf you didn't create an account, you can safely ignore this email.\n\nBest regards,\nJobsease Team";
        } elseif ($cart->type === 'resume') {
            $url = $cart->resume_id ? route('user.resumes.edit', ['resume_id' => $cart->resume_id]) : route('user.resumes.index');
            $subject = 'Your Resume Draft is Waiting';
            $body = "Hi " . $user->name . ",\n\n" . ($emailNum === 1 ? "You were creating a resume but didn't finish. Don't lose your progress!" : "You're almost done with your resume! Just a few more steps to go.") . "\n\nYou have all your information saved - just click below to finish!\n\nContinue Resume: " . $url . "\n\nBenefits of a complete resume:\n• Stand out to employers\n• Better job matches\n• Higher visibility\n\nIf you changed your mind, you can delete your draft anytime.\n\nBest regards,\nJobsease Team";
        }

        if ($subject && $body) {
            Mail::raw($body, function (Message $message) use ($user, $subject) {
                $message->subject($subject)
                    ->to($user->email)
                    ->from(config('mail.from.address'));
            });
            echo "[SENT] {$subject} to {$user->email}\n";
        }
    }
  }
