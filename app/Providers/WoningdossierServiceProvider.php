<?php

namespace App\Providers;

use App\Http\ViewComposers\CooperationComposer;
use App\Models\Cooperation;
use Illuminate\Support\ServiceProvider;

class WoningdossierServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
	    //view()->composer('cooperation.layouts.app',  CooperationComposer::class);
	    //view()->composer('*',  CooperationComposer::class);
	    \View::creator('*', CooperationComposer::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    	//\Log::debug(__METHOD__);

    	$this->app->bind('Cooperation', function(){
    		$cooperation = null;
		    if (\Session::has('cooperation')) {
			    $cooperation = Cooperation::find( \Session::get( 'cooperation' ) );
		    }

		    return $cooperation;
	    });

    	$this->app->bind('CooperationStyle', function(){
    		$cooperationStyle = null;
		    if (\Session::has('cooperation')) {
			    $cooperation = Cooperation::find(\Session::get('cooperation'));
			    if ($cooperation instanceof Cooperation){
			    	$cooperationStyle = $cooperation->style;
			    }
		    }

		    return $cooperationStyle;
	    });
    }
}
