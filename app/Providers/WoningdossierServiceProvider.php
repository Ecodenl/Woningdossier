<?php

namespace App\Providers;

use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class WoningdossierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        SessionGuard::macro('account', function () {
            return auth()->user();
        });
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind('Cooperation', function () {
            $cooperation = null;
            if (Session::has('cooperation')) {
                $c = Session::get('cooperation');
                $cooperation = \App\Helpers\Cache\Cooperation::find($c);
            }

            return $cooperation;
        });
    }
}
