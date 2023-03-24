<?php

namespace App\Services\Econobis\Api;

use App\Models\Cooperation;
use App\Services\Econobis\Api\Resources\Hoomdossier;
use App\Traits\FluentCaller;
use Illuminate\Support\Facades\App;

class EconobisApi
{
    use FluentCaller;

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function forCooperation(Cooperation $cooperation): self
    {
        $wildcard = null;
        $apiKey = null;

        if (App::isLocal()) {
            $wildcard = 'test';
            $apiKey = config('hoomdossier.services.econobis.api-key');
        }

        if (App::environment() === 'accept') {
            $wildcard = 'test';
            $apiKey = config('hoomdossier.services.econobis.api-key');
            if ( ! is_null($cooperation->econobis_api_key) && ! is_null($cooperation->econobis_wildcard)) {
                $wildcard = $cooperation->econobis_wildcard;
                $apiKey = $cooperation->econobis_api_key;
            }
        }

        if (App::isProduction()) {
            $wildcard = $cooperation->econobis_wildcard;
            $apiKey = $cooperation->econobis_api_key;
        }

        $this->client = $this->client->usesApiKey($apiKey)->usesWildcard($wildcard);

        return $this;
    }

    public function hoomdossier()
    {
        return new Hoomdossier($this->client, 'hoomdossier');
    }
}
