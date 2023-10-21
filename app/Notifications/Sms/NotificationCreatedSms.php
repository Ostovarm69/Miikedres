<?php

namespace App\Notifications\Sms;

use App\Channels\SmsChannel;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Sms;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificationCreatedSms extends Notification
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    public function toSms($notifiable)
    {
        return [
            'mobile'  => $notifiable->username,
            'data'    => [
                'fullname' => $notifiable->fullname,
                'link'     => route('front.notifications.index'),
            ],
            'type'    => Sms::TYPES['NOTIFICATION_CREATED_SMS'],
            'user_id' => $notifiable->id
        ];
    }
}
