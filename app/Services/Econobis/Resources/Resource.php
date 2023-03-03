<?php

namespace App\Services\Econobis\Resources;

use App\Services\Econobis\Client;
use GuzzleHttp\RequestOptions;

abstract class Resource
{
    /** @var Client $client */
    protected Client $client;

    protected string $uri;

    public function __construct(Client $client, string $uri)
    {
        $this->client = $client;
        $this->uri    = $uri;
    }

    public function uri(string $params = ''): string
    {
        return implode('/', [$this->uri, $params]);
    }

    protected static function buildQuery(array $attributes): array
    {
        return [
            RequestOptions::QUERY => $attributes,
        ];
    }
}
