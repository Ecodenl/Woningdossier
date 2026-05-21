<?php

namespace App\Services\SmartTwin\Api\Resources;

use App\Services\SmartTwin\Api\Client;

abstract class Resource
{
    protected Client $client;

    protected string $uri;

    public function __construct(Client $client, string $uri)
    {
        $this->client = $client;
        $this->uri    = $uri;
    }

    public function uri(string $params = ''): string
    {
        if ($params === '') {
            return $this->uri;
        }

        return implode('/', [$this->uri, $params]);
    }
}
