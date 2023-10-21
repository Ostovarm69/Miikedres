<?php

namespace App\Services\Havinmode;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;

class ContentService extends HavinmodeServiceAbstract
{

    public function __construct(Client $client, Repository $config)
    {
        parent::__construct($client, $config);
    }

    public function get($code)
    {
        $options = [
            'query' => [

            ],
            'headers' => [
                'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36'
            ]
        ];

        $products = $this->client->get(
            $this->config->get('havinmode.content_base_url') . $code,
            $options
        );

        return $products->getBody()->getContents();
    }
}
