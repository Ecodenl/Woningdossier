<?php

namespace App\Services\Verbeterjehuis;

use App\Traits\FluentCaller;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Client
{
    use FluentCaller;

    protected string $baseUrl = "https://www.verbeterjehuis.nl/api/v1/";

    private array $config;

    private ?GuzzleClient $client = null;

    private ?LoggerInterface $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        $this->config = [
            'base_uri'        => $this->baseUrl,
            'headers'         => [
                'Accept' => 'application/json',
            ],
            'allow_redirects' => false,
        ];
    }

    public function getClient()
    {
        if (is_null($this->client)) {
            if ($this->logger instanceof LoggerInterface) {
                $stack = new HandlerStack();
                $stack->setHandler(new CurlHandler());

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

    public function request(string $method, string $uri, array $options = []): array
    {
        $response = $this->getClient()->request($method, $uri, $options);

        $response->getBody()->seek(0);

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true);
    }

    public function get(string $uri, array $options = []): array
    {
        return $this->request('GET', $uri, $options);
    }

}
