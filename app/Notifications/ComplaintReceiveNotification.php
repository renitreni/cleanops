<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use YieldStudio\LaravelBrevoNotifier\BrevoSmsChannel;
use YieldStudio\LaravelBrevoNotifier\BrevoSmsMessage;

class ComplaintReceiveNotification extends Notification
{
    use Queueable;

    protected $request;

    /**
     * Create a new notification instance.
     */
    public function __construct($request)
    {
        $this->request = $request;
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
        return (new BrevoSmsMessage)
            ->from(config('app.name'))
            ->to($this->request['contact_no'])
            ->content("Serial No: {$this->request['serial']}
                We've received your complaint and notified the system admin for review. We'll update you soon. Thank you for your patience.");
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
