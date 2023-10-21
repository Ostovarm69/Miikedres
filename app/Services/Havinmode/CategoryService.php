<?php

namespace App\Services\Havinmode;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;

class CategoryService
{
    private $client;
    private $config;

    public function __construct(Client $client, Repository $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function get()
    {
        $options = [
            'query' => [
                'version' => $this->config->get('havinmode.api_version'),
                'available' => 1,
                'category_id' => $this->config->get('havinmode.category.bag.category_id'),
            ],
            'headers' => [
                'AUTHORIZATION' => 'Bearer 44963|BLhpjpdkulGQob78Jinm8exevHYy5ZWgiyKSXNRW',
            ]
        ];

        $products = $this->client->get(
            $this->config->get('havinmode.base_url') .
            '/home',
            $options
        );

        return (json_decode($products->getBody()->getContents(), true))['data']['response']['categories'];
    }
}
