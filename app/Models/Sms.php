<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $guarded = ['id'];

    const TYPES = [
        'VERIFY_CODE' => [
            'key'    => 'verify-code',
            'string' => 'کد تایید',
            'method' => 'verifyCode'
        ],
        'USER_CREATED' => [
            'key'    => 'user-created',
            'string' => 'خوش آمدگویی کاربر',
            'method' => 'userCreated'
        ],
        'ORDER_PAID' => [
            'key'    => 'order-paid',
            'string' => 'اطلاع رسانی پرداخت سفارش به مدیر',
            'method' => 'orderPaid'
        ],
        'USER_ORDER_PAID' => [
            'key'    => 'user-order-paid',
            'string' => 'اطلاع رسانی پرداخت سفارش به کاربر',
            'method' => 'userOrderPaid'
        ],
        'WALLET_AMOUNT_DECREASED' => [
            'key'    => 'wallet-amount-decreased',
            'string' => 'اطلاع رسانی کاهش موجودی کیف پول',
            'method' => 'walletAmountDecreased'
        ],
        'WALLET_AMOUNT_INCREASED' => [
            'key'    => 'wallet-amount-decreased',
            'string' => 'اطلاع رسانی افزایش موجودی کیف پول',
            'method' => 'walletAmountIncreased'
        ],
        'USER_BIRTHDAY' => [
            'key'    => 'user-birthday',
            'string' => 'تبریک تولد کاربر',
            'method' => 'birthday'
        ],
        'ORDER_COMMENT_SMS' => [
            'key'    => 'order_product_comment_sms',
            'string' => 'تبریک تولد کاربر',
            'method' => 'comment'
        ],
        'NOTIFICATION_CREATED_SMS' => [
            'key'    => 'notification-created',
            'string' => 'پیامک ایجاد اطلاعیه',
            'method' => 'notification'
        ],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        foreach (self::TYPES as $type) {
            if ($this->type == $type['key']) {
                return $type['string'];
            }
        }
    }
}
