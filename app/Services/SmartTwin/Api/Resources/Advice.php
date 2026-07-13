<?php

namespace App\Services\SmartTwin\Api\Resources;

use GuzzleHttp\RequestOptions;

class Advice extends Resource
{
    public function getAdvisorToolResults(string $dossierId): array
    {
        return $this->client->get($this->uri("advisor-tool/{$dossierId}"));
    }

    public function getQuickScanResults(string $dossierId): array
    {
        return $this->client->get($this->uri("quick-scan/{$dossierId}"));
    }

    /**
     * Get (or create) a deeplink to the advisor tool for an address. Coach flow.
     */
    public function getAdvisorToolLink(array $payload): array
    {
        return $this->client->post($this->uri('advisor-tool/link'), [
            RequestOptions::JSON => $payload,
        ]);
    }

    /**
     * Get (or create) a deeplink to the quickscan tool for an address. Resident flow.
     */
    public function getQuickScanLink(array $payload): array
    {
        return $this->client->post($this->uri('quick-scan/link'), [
            RequestOptions::JSON => $payload,
        ]);
    }
}
