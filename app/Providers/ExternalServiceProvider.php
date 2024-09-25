<?php

namespace App\Providers;

use App\Services\Verbeterjehuis\Client as VerbeterJeHuisClient;
use App\Services\Verbeterjehuis\Verbeterjehuis;
use Ecodenl\LvbagPhpWrapper\Client as LvbagClient;
use Ecodenl\LvbagPhpWrapper\Lvbag;
use Ecodenl\EpOnlinePhpWrapper\Client as EpOnlineClient;
use Ecodenl\EpOnlinePhpWrapper\EpOnline;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ExternalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(LvbagClient::class, function (Application $app) {
            $useProductionEndpoint = true;
            // During testing, we should be mocking, but just in case someone forgets to mock...
            if ($app->environment('testing')) {
                $useProductionEndpoint = false;
            }
            return new LvbagClient(
                config('hoomdossier.services.bag.secret'),
                'epsg:28992',
                $useProductionEndpoint,
            );
        });

        $this->app->bind(Lvbag::class, function (Application $app) {
            return new Lvbag($app->make(LvbagClient::class));
        });

        $this->app->bind(VerbeterJeHuisClient::class, function (Application $app) {
            return new VerbeterJeHuisClient();
        });

        $this->app->bind(Verbeterjehuis::class, function (Application $app) {
            return new Verbeterjehuis($app->make(VerbeterJeHuisClient::class));
        });

        $this->app->bind(EpOnlineClient::class, function (Application $app) {
            return new EpOnlineClient(config('hoomdossier.services.ep_online.secret'));
        });

        $this->app->bind(EpOnline::class, function (Application $app) {
            return new EpOnline($app->make(EpOnlineClient::class));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
