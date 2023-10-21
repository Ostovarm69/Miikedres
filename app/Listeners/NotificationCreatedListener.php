<?php

namespace App\Listeners;

use App\Events\NotificationCreated;
use App\Notifications\Sms\NotificationCreatedSms;
use App\Notifications\Sms\OrderPaidSms;
use Illuminate\Support\Facades\Notification;

class NotificationCreatedListener
{
    /**
     * Handle the event.
     *
     * @param  NotificationCreated  $event
     * @return void
     */
    public function handle(NotificationCreated $event)
    {
        $user = $event->user;

        if (option('notification_sms', 'off') == 'on') {
            Notification::send($user, new NotificationCreatedSms($user));
        }
    }
}
