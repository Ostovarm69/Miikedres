<?php

namespace App\Services\Havinmode;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;

class HavinmodeServiceAbstract
{
    protected $client;
    protected $config;

    public function __construct(Client $client, Repository $config)
    {
        $this->client = $client;
        $this->config = $config;
    }
}
