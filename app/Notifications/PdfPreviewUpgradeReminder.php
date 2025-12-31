<?php

namespace App\Notifications;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PdfPreviewUpgradeReminder extends Notification implements ShouldQueue
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
        $sessionData = $this->abandonedCart->session_data ?? [];
        $resumeName = $sessionData['resume_name'] ?? 'Your Resume';

        return (new MailMessage)
            ->subject('Your Beautiful Resume is Ready - ' . ($recoveryCount === 1 ? 'Unlock the Download!' : 'Don\'t Miss This Opportunity!'))
            ->greeting("Hi {$userName}!")
            ->line($recoveryCount === 1
                ? "Great news! Your resume \"{$resumeName}\" looks amazing in our preview. You just need to upgrade to download it!"
                : "Your professionally designed resume is waiting to be downloaded. Upgrade now to get it!"
            )
            ->action('Upgrade & Download', route('user.pricing'))
            ->line('Why upgrade?')
            ->line('📄 Download unlimited resume PDFs')
            ->line('🎨 Access premium templates')
            ->line('💼 Get AI-powered optimization tips')
            ->line('🚀 Access job matching tools')
            ->line('🎯 Interview preparation')
            ->line('')
            ->line('Special Offer: Get 10% off your first month with code RESUME10')
            ->line('')
            ->line('Your resume score: ' . ($sessionData['score'] ?? 'Not calculated'))
            ->line('We recommend upgrading to unlock advanced feedback and improvements.')
            ->salutation('Best regards,')
            ->markdown('notifications.mail');
    }
}
