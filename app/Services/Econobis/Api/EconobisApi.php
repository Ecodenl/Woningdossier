<?php

namespace App\Services\Econobis\Api;

use App\Models\Cooperation;
use App\Services\Econobis\Api\Resources\Hoomdossier;
use App\Traits\FluentCaller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

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
        $wildcard = $cooperation->econobis_wildcard;
        $apiKey = null;
        if (! empty($cooperation->econobis_api_key)) {
            try {
                $apiKey = Crypt::decrypt($cooperation->econobis_api_key);
            } catch (DecryptException $decryptException) {
                report($decryptException);
            }
        }

        // When one is null, just use the test environment.
        if (empty($wildcard) || empty($apiKey)) {
            $wildcard = config('hoomdossier.services.econobis.wildcard', 'test');
            $apiKey = config('hoomdossier.services.econobis.api-key');
        }

        $this->client = $this->client->usesApiKey($apiKey)->usesWildcard($wildcard);

        return $this;
    }

    public function hoomdossier()
    {
        return new Hoomdossier($this->client, 'hoomdossier');
    }
}
