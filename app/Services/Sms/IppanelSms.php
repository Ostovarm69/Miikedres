<?php

namespace App\Services\Sms;

use App\Contracts\SmsContract;
use App\Contracts\SmsNotificationContract;

class IppanelSms extends SmsService implements SmsContract, SmsNotificationContract
{
    public function send()
    {
        $method = $this->method();
        $data   = $this->$method();

        $username      = option('SMS_PANEL_USERNAME');
        $password      = option('SMS_PANEL_PASSWORD');
        $from          = option('SMS_PANEL_FROM');
        $to            = array($this->mobile());
        $pattern_code  = $data['pattern_code'];
        $input_data    = $data['input_data'];

        $url      = "https://ippanel.com/patterns/pattern?username="
            . $username . "&password=" . urlencode($password)
            . "&from=$from&to=" . json_encode($to)
            . "&input_data=" . urlencode(json_encode($input_data))
            . "&pattern_code=$pattern_code";
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($handler);

        if (!$response) {
            return curl_error($handler);
        }

        return $response;
    }

    public function verifyCode()
    {
        return [
            'pattern_code' => option('user_verify_pattern_code'),
            'input_data'   => [
                'code' => $this->data['code']
            ],
        ];
    }

    public function userCreated()
    {
        return [
            'pattern_code' => option('user_register_pattern_code'),
            'input_data'   => [
                'fullname' => $this->data['fullname']
            ],
        ];
    }

    public function orderPaid()
    {
        return [
            'pattern_code' => option('order_paid_pattern_code'),
            'input_data'   => [
                'order_id' => $this->data['order_id']
            ],
        ];
    }

    public function userOrderPaid()
    {
        return [
            'pattern_code' => option('user_order_paid_pattern_code'),
            'input_data'   => [
                'order_id' => $this->data['order_id']
            ],
        ];
    }

    public function walletAmountDecreased()
    {
        return [
            'pattern_code' => option('wallet_decrease_pattern_code_ippanel'),
            'input_data'   => [
                'amount' => $this->data['amount']
            ],
        ];
    }

    public function walletAmountIncreased()
    {
        return [
            'pattern_code' => option('wallet_increase_pattern_code_ippanel'),
            'input_data'   => [
                'amount' => $this->data['amount']
            ],
        ];
    }

    public function birthday()
    {
        return [
            'pattern_code' => option('birth_day_pattern_code_ippanel'),
            'input_data'   => [
                'fullname' => $this->data['fullname'],
                'code'     => $this->data['code'],
                'amount'   => $this->data['amount'],
            ],
        ];
    }
    public function comment()
    {
        return [
            'pattern_code' => option('comment_pattern_ippanel'),
            'input_data'   => [
                'fullname' => $this->data['fullname'],
                'order_id'     => $this->data['order_id'],
                'link'   => $this->data['link'],
            ],
        ];
    }
}
