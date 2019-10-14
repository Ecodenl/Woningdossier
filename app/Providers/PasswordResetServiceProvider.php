<?php

namespace App\Providers;

use App\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\ServiceProvider;

class PasswordResetServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerPasswordBrokerManager();
    }

    protected function registerPasswordBrokerManager()
    {
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordBrokerManager($app);
        });
    }

    public function provides()
    {
        return ['auth.password'];
    }

}
