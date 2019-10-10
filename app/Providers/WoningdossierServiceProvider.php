<?php

namespace App\Providers;

use App\Helpers\HoomdossierSession;
use App\Http\ViewComposers\AdminComposer;
use App\Http\ViewComposers\CooperationComposer;
use App\Http\ViewComposers\MyAccountComposer;
use App\Http\ViewComposers\ToolComposer;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Observer\BuildingObserver;
use App\Observer\CooperationObserver;
use App\Observers\PrivateMessageObserver;
use App\Observers\PrivateMessageViewObserver;
use App\Observers\UserActionPlanAdviceObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rule;

class WoningdossierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Cooperation::observe(CooperationObserver::class);
        PrivateMessage::observe(PrivateMessageObserver::class);
        UserActionPlanAdvice::observe(UserActionPlanAdviceObserver::class);
        PrivateMessageView::observe(PrivateMessageViewObserver::class);
        Building::observe(BuildingObserver::class);
        User::observe(UserObserver::class);

        \View::creator('cooperation.tool.*', ToolComposer::class);
        \View::creator('*', CooperationComposer::class);
        \View::creator('cooperation.admin.*', AdminComposer::class);
        \View::creator('cooperation.my-account.*', MyAccountComposer::class);

        SessionGuard::macro('account', function(){
            return auth()->user();
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Cooperation', function () {
            $cooperation = null;
            if (\Session::has('cooperation')) {
                $c = \Session::get('cooperation');
                $cooperation = \App\Helpers\Cache\Cooperation::find($c);
            }

            return $cooperation;
        });

        $this->app->bind('CooperationStyle', function () {
            $cooperationStyle = null;
            if (\Session::has('cooperation')) {
                // we know this as we've cached it earlier
                $c = \Session::get('cooperation');

                $cooperationStyle = \App\Helpers\Cache\Cooperation::getStyle($c);
            }

            return $cooperationStyle;
        });

    }
}
