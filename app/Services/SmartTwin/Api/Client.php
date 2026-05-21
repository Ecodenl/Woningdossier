<?php

namespace App\Services\SmartTwin\Api;

use App\Traits\FluentCaller;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Client
{
    use FluentCaller;

    private array $config;

    private ?GuzzleClient $client = null;

    private ?LoggerInterface $logger;

    public string $apiKey = '';

    public function __construct(string $baseUri, ?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->config = [
            'base_uri' => rtrim($baseUri, '/') . '/',
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
            RequestOptions::ALLOW_REDIRECTS => [
                'max' => 5,
            ],
        ];
    }

    public function usesApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        $this->config[RequestOptions::HEADERS]['X-Api-Key'] = $apiKey;
        // Force re-creation so the header change is picked up on the next request.
        $this->client = null;

        return $this;
    }

    public function getClient(): GuzzleClient
    {
        if (is_null($this->client)) {
            if ($this->logger instanceof LoggerInterface) {
                $stack = HandlerStack::create();

                $stack->push(
                    Middleware::log(
                        $this->logger,
                        new MessageFormatter(MessageFormatter::DEBUG),
                        LogLevel::DEBUG,
                    )
                );

                $this->config['handler'] = $stack;
            }

            $this->client = new GuzzleClient($this->config);
        }

        return $this->client;
    }

    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->getClient()->request($method, ltrim($uri, '/'), $options);
    }

    public function post(string $uri, array $options = []): array
    {
        $response = $this->request('POST', $uri, $options);

        return $this->decode($response);
    }

    public function delete(string $uri, array $options = []): void
    {
        $this->request('DELETE', $uri, $options);
    }

    private function decode(ResponseInterface $response): array
    {
        $response->getBody()->seek(0);
        $contents = $response->getBody()->getContents();

        if ($contents === '') {
            return [];
        }

        return json_decode($contents, true) ?? [];
    }
}
