<?php

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator;
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


	    Schema::defaultStringLength(191);

	    if ($this->app->environment('local', 'testing')) {
		    $this->app->register(DuskServiceProvider::class);
	    }
	    if ($this->app->environment('local')){
	    	//$this->app->register(IdeHelperServiceProvider::class);
	    }


    }
}
