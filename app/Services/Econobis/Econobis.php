<?php

namespace App\Services\Econobis;

use App\Services\Econobis\Resources\Hoomdossier;
use App\Traits\FluentCaller;

class Econobis
{
    use FluentCaller;

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function hoomdossier()
    {
        return new Hoomdossier($this->client, 'hoomdossier');
    }
}
