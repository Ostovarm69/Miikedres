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

class Comment extends Notification
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
                'order_id'   => $this->order->id,
                'link'     => route('front.orders.show',['order'=>$this->order]),
            ],
            'type'    => Sms::TYPES['ORDER_COMMENT_SMS'],
            'user_id' => $notifiable->id
        ];
    }
}
