<?php

namespace App\Services\Sms;

use App\Contracts\SmsContract;
use App\Contracts\SmsNotificationContract;
use Exception;
use Melipayamak\MelipayamakApi;

class MelipayamakSms extends SmsService implements SmsContract, SmsNotificationContract
{
    public function send()
    {
        $method = $this->method();
        $data   = $this->$method();

        $input_data   = $data['input_data'];
        $mobile       = $this->mobile();
        $bodyId       = $data['bodyId'];

        try {
            $username  = option('MELIPAYAMAK_PANEL_USERNAME');
            $password  = option('MELIPAYAMAK_PANEL_PASSWORD');
            $api       = new MelipayamakApi($username, $password);
            $sms       = $api->sms('soap');
            $to        = $mobile;
            $text      = implode(';', $input_data);
            $response  = $sms->sendByBaseNumber($text, $to, $bodyId);

            $message = json_encode($response);
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return $message;
    }

    public function verifyCode()
    {
        return [
            'bodyId'       => option('user_verify_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['code']
            ],
        ];
    }

    public function userCreated()
    {
        return [
            'bodyId'       => option('user_register_pattern_code_melipayamak'),
            'input_data'   => [
                '0'   => $this->data['fullname'],
                '1'   => $this->data['username'],
            ],
        ];
    }

    public function orderPaid()
    {
        return [
            'bodyId'       => option('order_paid_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['order_id']
            ],
        ];
    }

    public function userOrderPaid()
    {
        return [
            'bodyId'       => option('user_order_paid_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['order_id']
            ],
        ];
    }

    public function walletAmountDecreased()
    {
        return [
            'bodyId'       => option('wallet_decrease_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['amount']
            ],
        ];
    }

    public function walletAmountIncreased()
    {
        return [
            'bodyId'       => option('wallet_increase_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['amount']
            ],
        ];
    }

    public function birthday()
    {
        return [
            'bodyId'       => option('birth_day_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['fullname'],
                '1' => $this->data['amount'],
                '2' => $this->data['code'],
            ],
        ];
    }
    public function comment()
    {
        return [
            'bodyId'       => option('comment_pattern_melipayamak'),
            'input_data'   => [
                '0' => $this->data['fullname'],
                '1' => $this->data['order_id'],
                '2' => $this->data['link'],
            ],
        ];
    }

    public function notification()
    {
        return [
            'bodyId'       => option('notification_pattern_melipayamak'),
            'input_data'   => [
                '0' => $this->data['fullname'],
                '1' => $this->data['link'],
            ],
        ];
    }
}
