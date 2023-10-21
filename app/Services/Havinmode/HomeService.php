<?php

namespace App\Services\Havinmode;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Log;

class HomeService extends HavinmodeServiceAbstract
{

    public function __construct(Client $client, Repository $config)
    {
        parent::__construct($client, $config);
    }

    public function get()
    {
        $options = [
            'headers' => [
                'AUTHORIZATION' => 'Bearer 44963|BLhpjpdkulGQob78Jinm8exevHYy5ZWgiyKSXNRW',
            ]
        ];

        $home = $this->client->get(
            $this->config->get('havinmode.base_url') .
            $this->config->get('havinmode.home'),
            $options
        );

        return json_decode($home->getBody()->getContents(), true);
    }
}
