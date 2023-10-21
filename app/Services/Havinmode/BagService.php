<?php

namespace App\Services\Havinmode;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;

class BagService
{
    private $client;
    private $config;

    public function __construct(Client $client, Repository $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function list()
    {
        $options = [
            'query' => [
                'version' => $this->config->get('havinmode.api_version'),
                'available' => 1,
                'category_id' => $this->config->get('havinmode.category.bag.category_id'),
            ]
        ];

        $products = $this->client->get(
            $this->config->get('havinmode.base_url') .
            $this->config->get('havinmode.products'),
            $options
        );

        return json_decode($products->getBody()->getContents(), true);
    }
}
