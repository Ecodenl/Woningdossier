<?php

namespace App\Providers;

use App\Http\ViewComposers\AdminComposer;
use App\Http\ViewComposers\CooperationComposer;
use App\Http\ViewComposers\MyAccountComposer;
use App\Http\ViewComposers\ToolComposer;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\UserActionPlanAdvice;
use App\Observer\CooperationObserver;
use App\Observers\PrivateMessageObserver;
use App\Observers\PrivateMessageViewObserver;
use App\Observers\UserActionPlanAdviceObserver;
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
        Cooperation::observe(CooperationObserver::class);
        PrivateMessage::observe(PrivateMessageObserver::class);
        UserActionPlanAdvice::observe(UserActionPlanAdviceObserver::class);
        PrivateMessageView::observe(PrivateMessageViewObserver::class);

        \View::creator('cooperation.tool.*', ToolComposer::class);
        \View::creator('*', CooperationComposer::class);
        \View::creator('cooperation.admin.*', AdminComposer::class);
        \View::creator('cooperation.my-account.*', MyAccountComposer::class);


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
