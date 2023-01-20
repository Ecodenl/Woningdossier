<?php

namespace App\Services\Verbeterjehuis;

use App\Services\Verbeterjehuis\Resources\Regulation;
use App\Traits\FluentCaller;

class Verbeterjehuis
{
    use FluentCaller;

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function regulation(): Regulation
    {
        return new Regulation($this->client, 'regulation');
    }
}
