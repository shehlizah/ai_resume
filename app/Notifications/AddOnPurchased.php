<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\UserAddOn;

class AddOnPurchased extends Notification
{
    use Queueable;

    protected $userAddOn;

    public function __construct(UserAddOn $userAddOn)
    {
        $this->userAddOn = $userAddOn;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $addOn = $this->userAddOn->addOn;

        return (new MailMessage)
            ->subject('Add-On Purchase Confirmation - ' . $addOn->name)
            ->greeting('Thank You for Your Purchase!')
            ->line('You have successfully purchased: ' . $addOn->name)
            ->line('Amount paid: $' . number_format($this->userAddOn->amount_paid, 2))
            ->action('Access Your Add-On', route('user.add-ons.access', $addOn))
            ->line('Thank you for choosing our services!');
    }

    public function toArray($notifiable)
    {
        return [
            'add_on_id' => $this->userAddOn->add_on_id,
            'add_on_name' => $this->userAddOn->addOn->name,
            'amount_paid' => $this->userAddOn->amount_paid,
            'purchased_at' => $this->userAddOn->purchased_at,
        ];
    }
}