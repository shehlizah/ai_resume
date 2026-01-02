<?php

namespace App\Notifications;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use function route;

class IncompleteSignupReminder extends Notification implements ShouldQueue
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

        return $this->buildMailMessage($userName, $recoveryCount);
    }

    /**
     * Build the MailMessage for the reminder email
     * @param string $userName
     * @param int $recoveryCount
     * @return MailMessage
     */
    public function buildMailMessage($userName = 'there', $recoveryCount = 1)
    {
        return (new MailMessage)
            ->subject('Complete Your Account Setup - ' . ($recoveryCount === 1 ? 'Just One Step Left!' : 'We\'re Still Waiting for You'))
            ->greeting("Hi {$userName}!")
            ->line($recoveryCount === 1
                ? 'You started creating your account but didn\'t complete it. Setting a password only takes 30 seconds!'
                : 'We noticed your account is still incomplete. Your password is the final step!'
            )
            ->action('Complete Account Setup', route('register'))
            ->line('Benefits of completing your signup:')
            ->line('✓ Access to resume builder')
            ->line('✓ Job matching and alerts')
            ->line('✓ Interview preparation tools')
            ->line('✓ Premium templates')
            ->line('If you didn\'t create an account, you can safely ignore this email.')
            ->salutation('Best regards,
Jobsease Team');
    }
}
