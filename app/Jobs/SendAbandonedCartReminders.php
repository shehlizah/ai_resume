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
                    echo "[JOB] Sending email for cart #{$cart->id}, type: {$cart->type}\n";
                    
                    // Send email directly based on cart type
                    match ($cart->type) {
                        'signup' => $this->sendSignupEmail($cart),
                        'resume' => $this->sendResumeEmail($cart),
                        'pdf_preview' => $this->sendPdfEmail($cart),
                        'payment' => $this->sendPaymentEmail($cart),
                        default => null,
                    };

                    echo "[JOB] Email sent successfully for cart #{$cart->id}\n";

                    // Mark email as sent
                    $cart->markRecoveryEmailSent();
                    echo "[JOB] Cart #{$cart->id} marked as sent (count: {$cart->recovery_email_sent_count})\n";
                } catch (\Exception $e) {
                    echo "[JOB ERROR] Failed for cart #{$cart->id}: " . $e->getMessage() . "\n";
                    echo $e->getTraceAsString() . "\n";
                }
            }

            echo "[JOB] Job completed successfully\n";
        } catch (\Exception $e) {
            echo "[JOB FATAL ERROR] " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
            throw $e;
        }
    }

    private function sendPaymentEmail(AbandonedCart $cart)
    {
        $emailNumber = $cart->recovery_email_sent_count + 1;
        $user = $cart->user;
        
        // Build checkout URL
        try {
            $checkoutUrl = route('pricing');
        } catch (\Exception $e) {
            $checkoutUrl = config('app.url') . '/pricing';
        }

        if ($cart->session_data['resume_id'] ?? null) {
            $checkoutUrl .= '?resume=' . $cart->session_data['resume_id'];
        }

        if ($emailNumber === 1) {
            $subject = 'Complete Your Jobsease Upgrade';
            $body = "Hi,\n\n" .
                "We noticed you were just one step away from upgrading your Jobsease account, but the checkout wasn't completed.\n\n" .
                "Your selected plan is still reserved and gives you access to:\n" .
                "• Faster job matching\n" .
                "• Priority visibility to employers\n" .
                "• Advanced profile & application features\n\n" .
                "You can continue anytime from where you left off — no need to start again.\n\n" .
                "Complete your upgrade: {$checkoutUrl}\n\n" .
                "If you faced any issue during checkout, feel free to reply to this email. We're happy to help.\n\n" .
                "Best regards,\nJobsease Team";
        } elseif ($emailNumber === 2) {
            $subject = 'Your Jobsease Upgrade is Still Pending';
            $body = "Hi,\n\n" .
                "Just a quick reminder about your pending Jobsease upgrade.\n\n" .
                "Upgrading your account helps you:\n" .
                "• Get noticed by employers faster\n" .
                "• Apply to premium job listings\n" .
                "• Improve your chances with enhanced profile tools\n\n" .
                "Many candidates using upgraded plans get responses significantly quicker than free users.\n\n" .
                "Your upgrade is still pending — you can activate it in just a minute.\n\n" .
                "Upgrade now: {$checkoutUrl}\n\n" .
                "If you have questions about plans or pricing, simply reply to this email.\n\n" .
                "Regards,\nJobsease Team";
        } else {
            $subject = 'Final Reminder: Your Jobsease Upgrade';
            $body = "Hi,\n\n" .
                "This is a final reminder regarding your incomplete Jobsease upgrade.\n\n" .
                "Your selected plan may expire soon, and you could miss out on:\n" .
                "• Priority employer access\n" .
                "• Advanced job application tools\n" .
                "• Better visibility in searches\n\n" .
                "If you're still looking to move ahead in your career, now is the best time to upgrade.\n\n" .
                "Complete your upgrade: {$checkoutUrl}\n\n" .
                "Need help or unsure which plan is right for you? Just reply — our team is here.\n\n" .
                "Best wishes,\nJobsease Team";
        }

        Mail::raw($body, function (Message $message) use ($user, $subject) {
            $message->subject($subject)
                ->to($user->email)
                ->from(config('mail.from.address'));
        });

        echo "[EMAIL] Payment email #{$emailNumber} sent to {$user->email}\n";
    }

    private function sendSignupEmail(AbandonedCart $cart)
    {
        $user = $cart->user;
        $emailNumber = $cart->recovery_email_sent_count + 1;

        $body = "Hi {$user->name},\n\n" .
            ($emailNumber === 1
                ? "You started creating your account but didn't complete it. Setting a password only takes 30 seconds!\n\n"
                : "We noticed your account is still incomplete. Your password is the final step!\n\n") .
            "Benefits of completing your signup:\n" .
            "✓ Access to resume builder\n" .
            "✓ Job matching and alerts\n" .
            "✓ Interview preparation tools\n" .
            "✓ Premium templates\n\n" .
            "Complete your account setup: " . route('password.request') . "\n\n" .
            "If you didn't create an account, you can safely ignore this email.\n\n" .
            "Best regards,\nJobsease Team";

        Mail::raw($body, function (Message $message) use ($user) {
            $message->subject('Complete Your Account Setup')
                ->to($user->email)
                ->from(config('mail.from.address'));
        });

        echo "[EMAIL] Signup email sent to {$user->email}\n";
    }

    private function sendResumeEmail(AbandonedCart $cart)
    {
        $user = $cart->user;
        $emailNumber = $cart->recovery_email_sent_count + 1;
        $resumeData = $cart->session_data ?? [];
        $resumeName = $resumeData['name'] ?? 'Your Resume';

        if ($cart->resume_id) {
            $continueUrl = route('user.resumes.edit', ['resume_id' => $cart->resume_id]);
        } else {
            $continueUrl = route('user.resumes.index');
        }

        $body = "Hi {$user->name}!\n\n" .
            ($emailNumber === 1
                ? "You were creating \"{$resumeName}\" but didn't finish. Don't lose your progress!\n\n"
                : "You're almost done with your resume! Just a few more steps to go.\n\n") .
            "What you've completed so far:\n" .
            "✓ Template selected: " . ($resumeData['template'] ?? 'Professional') . "\n" .
            "✓ Personal information: " . ($resumeData['name'] ?? 'Not filled') . "\n\n" .
            "You have all your information saved - just click the button below to finish!\n\n" .
            "Continue Resume: {$continueUrl}\n\n" .
            "Benefits of a complete resume:\n" .
            "• Stand out to employers\n" .
            "• Better job matches\n" .
            "• Higher visibility\n\n" .
            "If you changed your mind, you can delete your draft anytime.\n\n" .
            "Best regards,\nJobsease Team";

        Mail::raw($body, function (Message $message) use ($user) {
            $message->subject('Your Resume Draft is Waiting - Pick Up Where You Left Off!')
                ->to($user->email)
                ->from(config('mail.from.address'));
        });

        echo "[EMAIL] Resume email sent to {$user->email}\n";
    }

    private function sendPdfEmail(AbandonedCart $cart)
    {
        $user = $cart->user;

        $body = "Hi {$user->name},\n\n" .
            "Your PDF preview is ready!\n\n" .
            "View your PDF: " . route('user.resumes.index') . "\n\n" .
            "Best regards,\nJobsease Team";

        Mail::raw($body, function (Message $message) use ($user) {
            $message->subject('Your Resume PDF is Ready')
                ->to($user->email)
                ->from(config('mail.from.address'));
        });

        echo "[EMAIL] PDF email sent to {$user->email}\n";
    }
}


