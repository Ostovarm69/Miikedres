<?php

namespace App\Services\Havinmode;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Log;

class ProductService extends HavinmodeServiceAbstract
{

    public function __construct(Client $client, Repository $config)
    {
        parent::__construct($client, $config);
    }

    public function index($perPage = 50, $currentPages = 1)
    {
        $options = [
            'query' => [
                'version' => $this->config->get('havinmode.api_version'),
                'available' => 0,
                'per_page' => $perPage,
                'page' => $currentPages
            ],
            'headers' => [
                'authorization' => 'Bearer 44963|BLhpjpdkulGQob78Jinm8exevHYy5ZWgiyKSXNRW',
                'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36'
            ]
        ];

        $products = $this->client->get(
            $this->config->get('havinmode.base_url') .
            $this->config->get('havinmode.products'),
            $options
        );

        return json_decode($products->getBody()->getContents(), true);
    }

    public function listByCategory(int $category, int $products_count)
    {
        $options = [
            'query' => [
                'version' => $this->config->get('havinmode.api_version'),
                'available' => 0,
                'category_id' => $category,
                'per_page' => $products_count
            ],
            'headers' => [
                'authorization' => 'Bearer 44963|BLhpjpdkulGQob78Jinm8exevHYy5ZWgiyKSXNRW',
                'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36'
            ]
        ];

        $products = $this->client->get(
            $this->config->get('havinmode.base_url') .
            $this->config->get('havinmode.products'),
            $options
        );

        return json_decode($products->getBody()->getContents(), true);
    }

    public function getDetails($product_id)
    {
        try {
            $options = [
                'headers' => [
                    'authorization' => 'Bearer 44963|BLhpjpdkulGQob78Jinm8exevHYy5ZWgiyKSXNRW',
                    'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36'
                ]
            ];

            $products = $this->client->get(
                $this->config->get('havinmode.base_url') .
                $this->config->get('havinmode.products') .
                "/$product_id",
                $options
            );
            $data = $products->getBody()->getContents();

            return json_decode($data, true);
        } catch (Exception $x){
            echo $x->getMessage() . PHP_EOL;
            die();
        }
    }

    public function getPages()
    {
        $list = $this->index();

        return $list['data']['products']['last_page'];
    }
}
