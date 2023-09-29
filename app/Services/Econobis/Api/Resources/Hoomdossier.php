<?php

namespace App\Services\Econobis\Api\Resources;

use GuzzleHttp\RequestOptions;

class Hoomdossier extends Resource
{
    public function gebruik(array $data)
    {
        return $this->client->post($this->uri('gebruik'), [RequestOptions::JSON => $data]);
    }

    public function woningStatus(array $data)
    {
        return $this->client->post($this->uri('woning-status'), [RequestOptions::JSON => $data]);
    }
    public function afspraak(array $data)
    {
        return $this->client->post($this->uri('afspraak'), [RequestOptions::JSON => $data]);
    }

    public function scanStatus(array $data)
    {
        return $this->client->post($this->uri('scan-status'), [RequestOptions::JSON => $data]);
    }

    public function woonplan(array $data)
    {
        return $this->client->post($this->uri('woonplan'), [RequestOptions::JSON => $data]);
    }

    public function pdf(array $data)
    {
        return $this->client->post($this->uri('pdf'), [RequestOptions::JSON => $data]);
    }

    public function delete(array $data)
    {
        return $this->client->post($this->uri('delete'), [RequestOptions::JSON => $data]);
    }
}