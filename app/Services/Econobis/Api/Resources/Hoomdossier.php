<?php

namespace App\Services\Econobis\Api\Resources;

use GuzzleHttp\RequestOptions;

class Hoomdossier extends Resource
{
    public function gebruik(array $data): array
    {
        return $this->client->post($this->uri('gebruik'), [RequestOptions::JSON => $data]);
    }

    public function woningStatus(array $data): array
    {
        return $this->client->post($this->uri('woning-status'), [RequestOptions::JSON => $data]);
    }

    public function afspraak(array $data): array
    {
        return $this->client->post($this->uri('afspraak'), [RequestOptions::JSON => $data]);
    }

    public function scanStatus(array $data): array
    {
        return $this->client->post($this->uri('scan-status'), [RequestOptions::JSON => $data]);
    }

    public function woonplan(array $data): array
    {
        return $this->client->post($this->uri('woonplan'), [RequestOptions::JSON => $data]);
    }

    public function pdf(array $data): array
    {
        return $this->client->post($this->uri('pdf'), [RequestOptions::JSON => $data, 'timeout' => 360]);
    }

    public function delete(array $data): array
    {
        return $this->client->post($this->uri('delete'), [RequestOptions::JSON => $data]);
    }
}
