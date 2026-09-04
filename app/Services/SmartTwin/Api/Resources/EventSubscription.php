<?php

namespace App\Services\SmartTwin\Api\Resources;

use GuzzleHttp\RequestOptions;

class EventSubscription extends Resource
{
    public function subscribe(string $subscriberName, string $callbackUrl, ?string $apiKey = null): array
    {
        $payload = [
            'subscriberName' => $subscriberName,
            'callbackUrl'    => $callbackUrl,
        ];

        if ($apiKey !== null) {
            $payload['apiKey'] = $apiKey;
        }

        return $this->client->post($this->uri('subscribe'), [
            RequestOptions::JSON => $payload,
        ]);
    }

    public function unsubscribe(string $subscriptionId): void
    {
        $this->client->post($this->uri('unsubscribe'), [
            RequestOptions::JSON => ['subscriptionId' => $subscriptionId],
        ]);
    }
}
