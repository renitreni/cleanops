<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use YieldStudio\LaravelBrevoNotifier\BrevoSmsChannel;
use YieldStudio\LaravelBrevoNotifier\BrevoSmsMessage;

class ComplaintReceiveNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [BrevoSmsChannel::class];
    }

    public function toBrevoSms($notifiable): BrevoSmsMessage
    {
        return (new BrevoSmsMessage())
            ->from('YIELD')
            ->to('+639064243594')
            ->content('Your order is confirmed.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
