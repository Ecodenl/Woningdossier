<?php

namespace App\Providers;

use App\Services\SmartTwin\Api\Client;
use App\Services\SmartTwin\Api\SmartTwinApi;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class SmartTwinServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->bind(Client::class, function (Application $app) {
            $baseUri = config('hoomdossier.services.smarttwin.base_uri');
            $apiKey  = config('hoomdossier.services.smarttwin.api-key', '');
            $debug   = $app->isLocal() || config('hoomdossier.services.smarttwin.debug', false);

            $client = $debug
                ? new Client($baseUri, Log::getLogger())
                : new Client($baseUri);

            return $client->usesApiKey($apiKey);
        });

        $this->app->bind(SmartTwinApi::class, function (Application $app) {
            return new SmartTwinApi($app->make(Client::class));
        });
    }

    public function boot(): void
    {
        //
    }

    public function provides(): array
    {
        return [SmartTwinApi::class, Client::class];
    }
}
