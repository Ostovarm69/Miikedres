<?php

namespace App\Jobs;

use App\Models\ClientNotification;
use App\Models\Discount;
use App\Models\User;
use App\Notifications\Sms\BirthDay;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class BirthdaySmsToUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (option('birth_day_sms') != 'on' || !option('user_birth_amount')) return;

        $users = User::whereDate('birth_date', Carbon::now())->get();

        foreach ($users as $user) {
            $code = Discount::generateCode();

            $discount = Discount::create([
                'title'       => "به مناسبت تولد",
                'code'        => $code,
                'type'        => 'percent',
                'amount'      => option('user_birth_amount'),
                'description' => "این کد تخفیف برای روز تولد کاربر با نام " . $user->fullname . " ایجاد شده است.",
                'quantity'    => 1,
                'start_date'  => now(),
                'end_date'    => now()->addDays(90)
            ]);

            $discount->users()->attach([$user->id]);

            ClientNotification::create([
                'user_id' => $user->id,
                'type'    => 'birthday',
                'message' => "{$user->first_name} عزیز کدتخفیف {$discount->amount} درصدی {$code} به مناسبت روز تولد شما ایجاد شد."
            ]);

//            Notification::send($user, new BirthDay($discount));
        }
    }
}
