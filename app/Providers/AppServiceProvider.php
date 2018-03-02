<?php

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
	    if ($this->app->environment('local', 'testing')) {
		    $this->app->register(DuskServiceProvider::class);
	    }
	    if ($this->app->environment('local')){
	    	//$this->app->register(IdeHelperServiceProvider::class);
	    }
    }
}
