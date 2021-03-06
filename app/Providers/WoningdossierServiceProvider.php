<?php

namespace App\Providers;

use App\Http\ViewComposers\AdminComposer;
use App\Http\ViewComposers\CooperationComposer;
use App\Http\ViewComposers\ToolComposer;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\Step;
use App\Models\Translation;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Observers\AccountObserver;
use App\Observers\BuildingObserver;
use App\Observers\CooperationObserver;
use App\Observers\PrivateMessageObserver;
use App\Observers\PrivateMessageViewObserver;
use App\Observers\StepObserver;
use App\Observers\TranslationObserver;
use App\Observers\UserActionPlanAdviceObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\SessionGuard;
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
        Step::observe(StepObserver::class);
        PrivateMessage::observe(PrivateMessageObserver::class);
        UserActionPlanAdvice::observe(UserActionPlanAdviceObserver::class);
        PrivateMessageView::observe(PrivateMessageViewObserver::class);
        Building::observe(BuildingObserver::class);
        User::observe(UserObserver::class);
        Account::observe(AccountObserver::class);
        Translation::observe(TranslationObserver::class);

        \View::creator('cooperation.tool.*', ToolComposer::class);
        \View::creator('*', CooperationComposer::class);
        \View::creator('cooperation.admin.*', AdminComposer::class);
        //\View::creator('cooperation.my-account.*', MyAccountComposer::class);

        SessionGuard::macro('account', function () {
            return auth()->user();
        });

        // new laravel versions have a requiredIf method in which we can pass a condition closure etc.
        // https://github.com/laravel/framework/blob/5.8/src/Illuminate/Validation/Rules/RequiredIf.php
        // for now just easy true false.
        Rule::macro('requiredIf', function ($shouldPass) {
            return $shouldPass ? 'required' : '';
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ToolComposer::class);
        $this->app->singleton(CooperationComposer::class);

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
