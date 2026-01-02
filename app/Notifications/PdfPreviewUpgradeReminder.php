<?php

namespace App\Notifications;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use function route;

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
        $userName = $notifiable->name ?? 'there';
        $recoveryCount = $this->abandonedCart->recovery_email_sent_count + 1;
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
        $sessionData = $this->abandonedCart->session_data ?? [];
        $checkoutUrl = route('pricing');
        if ($this->abandonedCart->session_data['resume_id'] ?? null) {
            $checkoutUrl .= '?resume=' . $this->abandonedCart->session_data['resume_id'];
        }

        if ($recoveryCount === 1) {
            // Email 1 – Friendly Reminder (After 1 Hour)
            return (new MailMessage)
                ->subject('Complete Your Jobsease Upgrade')
                ->greeting('Hi,')
                ->line('We noticed you were just one step away from upgrading your Jobsease account, but the checkout wasn\'t completed.')
                ->line('Your selected plan is still reserved and gives you access to:')
                ->line('- Faster job matching')
                ->line('- Priority visibility to employers')
                ->line('- Advanced profile & application features')
                ->line('You can continue anytime from where you left off — no need to start again.')
                ->action('Complete your upgrade here', $checkoutUrl)
                ->line('If you faced any issue during checkout, feel free to reply to this email. We\'re happy to help.')
                ->salutation('Best regards,\nJOBSEASE TEAM');
        } elseif ($recoveryCount === 2) {
            // Email 2 – Value + Upgrade Benefits (After 24 Hours)
            return (new MailMessage)
                ->subject('Your Jobsease Upgrade is Still Pending')
                ->greeting('Hi,')
                ->line('Just a quick reminder about your pending Jobsease upgrade.')
                ->line('Upgrading your account helps you:')
                ->line('- Get noticed by employers faster')
                ->line('- Apply to premium job listings')
                ->line('- Improve your chances with enhanced profile tools')
                ->line('Many candidates using upgraded plans get responses significantly quicker than free users.')
                ->line('Your upgrade is still pending — you can activate it in just a minute.')
                ->action('Upgrade now', $checkoutUrl)
                ->line('If you have questions about plans or pricing, simply reply to this email.')
                ->salutation('Regards,\nJobsease Team');
        } else {
            // Email 3 – Urgency / Final Reminder (After 72 Hours)
            return (new MailMessage)
                ->subject('Final Reminder: Your Jobsease Upgrade')
                ->greeting('Hi,')
                ->line('This is a final reminder regarding your incomplete Jobsease upgrade.')
                ->line('Your selected plan may expire soon, and you could miss out on:')
                ->line('- Priority employer access')
                ->line('- Advanced job application tools')
                ->line('- Better visibility in searches')
                ->line('If you\'re still looking to move ahead in your career, now is the best time to upgrade.')
                ->action('Complete your upgrade here', $checkoutUrl)
                ->line('Need help or unsure which plan is right for you? Just reply — our team is here.')
                ->salutation('Best wishes,\nJobsease Team');
        }
    }
}
