<?php

namespace App\Providers;

use App\Services\Econobis\Api\Client;
use App\Services\Econobis\Api\EconobisApi;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class EconobisServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function (Application $app) {
            if ($app->isLocal()) {
                return new Client(Log::getLogger());
            } else {
                return new Client();
            }
        });

        $this->app->singleton(EconobisApi::class, function (Application $app) {
            return new EconobisApi($app->make(Client::class));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function provides()
    {
        return [EconobisApi::class, Client::class];
    }
}
