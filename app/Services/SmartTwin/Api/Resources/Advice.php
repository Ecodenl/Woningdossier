<?php

namespace App\Services\SmartTwin\Api\Resources;

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
}
