<?php

namespace App\Services\Econobis\Resources;

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
}