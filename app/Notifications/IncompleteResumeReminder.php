<?php

namespace App\Notifications;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncompleteResumeReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $abandonedCart;

    public function __construct(AbandonedCart $abandonedCart)
    {
        $this->abandonedCart = $abandonedCart;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $recoveryCount = $this->abandonedCart->recovery_email_sent_count + 1;
        $userName = $notifiable->name ?? 'there';
        $resumeData = $this->abandonedCart->session_data ?? [];
        $resumeName = $resumeData['name'] ?? 'Your Resume';

        // Build continue URL safely
        if ($this->abandonedCart->resume_id) {
            $continueUrl = route('user.resumes.edit', ['resume_id' => $this->abandonedCart->resume_id]);
        } else {
            $continueUrl = route('user.resumes.index');
        }

        return (new MailMessage)
            ->subject('Your Resume Draft is Waiting - ' . ($recoveryCount === 1 ? 'Pick Up Where You Left Off!' : 'Complete Your Resume Now'))
            ->greeting("Hi {$userName}!")
            ->line($recoveryCount === 1
                ? "You were creating \"{$resumeName}\" but didn't finish. Don't lose your progress!"
                : "You're almost done with your resume! Just a few more steps to go."
            )
            ->action('Continue Resume', $continueUrl)
            ->line('What you\'ve completed so far:')
            ->line('✓ Template selected: ' . ($resumeData['template'] ?? 'Professional'))
            ->line('✓ Personal information: ' . ($resumeData['name'] ?? 'Not filled'))
            ->line('You have all your information saved - just click the button above to finish!')
            ->line('')
            ->line('Benefits of a complete resume:')
            ->line('• Stand out to employers')
            ->line('• Better job matches')
            ->line('• Higher visibility')
            ->line('If you changed your mind, you can delete your draft anytime.')
            ->salutation('Best regards,')
            ->markdown('notifications.mail');
    }
}
