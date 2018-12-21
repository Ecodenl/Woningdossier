<?php

namespace App\Providers;

use App\Helpers\HoomdossierSession;
use App\Http\ViewComposers\CooperationComposer;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\UserActionPlanAdvice;
use App\Observers\UserActionPlanAdviceObserver;
use Doctrine\DBAL\Schema\Schema;
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
        \View::composer('cooperation.tool.progress', function ($view) {
            $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

            // get the steps from a cooperation that are active and ordered on the order column from the pivot table
            $steps = $cooperation->getActiveOrderedSteps();

            $view->with('steps', $steps);
        });
        //view()->composer('cooperation.layouts.app',  CooperationComposer::class);
        //view()->composer('*',  CooperationComposer::class);

        \View::composer('cooperation.tool.includes.interested', function ($view) {
            $view->with('interests', Interest::orderBy('order')->get());
        });

        \View::composer('*', function ($view) {
            $view->with('inputSources', InputSource::orderBy('order', 'desc')->get());
        });

        \View::creator('*', CooperationComposer::class);

        UserActionPlanAdvice::observe(UserActionPlanAdviceObserver::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //\Log::debug(__METHOD__);

        $this->app->bind('Cooperation', function () {
            $cooperation = null;
            if (\Session::has('cooperation')) {
                $cooperation = Cooperation::find(\Session::get('cooperation'));
            }

            return $cooperation;
        });

        $this->app->bind('CooperationStyle', function () {
            $cooperationStyle = null;
            if (\Session::has('cooperation')) {
                $cooperation = Cooperation::find(\Session::get('cooperation'));
                if ($cooperation instanceof Cooperation) {
                    $cooperationStyle = $cooperation->style;
                }
            }

            return $cooperationStyle;
        });
    }
}
