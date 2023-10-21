<?php

namespace App\Notifications\Sms;

use App\Channels\SmsChannel;
use App\Models\Discount;
use App\Models\Sms;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BirthDay extends Notification
{
    use Queueable;

    public $discount;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Discount $discount)
    {
        $this->discount = $discount;
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
                'amount'   => $this->discount->amount,
                'code'     => $this->discount->code,
            ],
            'type'    => Sms::TYPES['USER_BIRTHDAY'],
            'user_id' => $notifiable->id
        ];
    }
}
