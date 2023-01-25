<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\Translation;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Observers\AccountObserver;
use App\Observers\BuildingFeatureObserver;
use App\Observers\BuildingElementObserver;
use App\Observers\BuildingObserver;
use App\Observers\CompletedSubStepObserver;
use App\Observers\CooperationMeasureApplicationObserver;
use App\Observers\CooperationObserver;
use App\Observers\CustomMeasureApplicationObserver;
use App\Observers\MeasureApplicationObserver;
use App\Observers\PrivateMessageObserver;
use App\Observers\PrivateMessageViewObserver;
use App\Observers\ToolQuestionAnswerObserver;
use App\Observers\TranslationObserver;
use App\Observers\UserActionPlanAdviceObserver;
use App\Observers\UserObserver;
use App\Models\ToolQuestionAnswer;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Session;
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
        BuildingFeature::observe(BuildingFeatureObserver::class);
        BuildingElement::observe(BuildingElementObserver::class);
        User::observe(UserObserver::class);
        Account::observe(AccountObserver::class);
        Translation::observe(TranslationObserver::class);
        CompletedSubStep::observe(CompletedSubStepObserver::class);
        ToolQuestionAnswer::observe(ToolQuestionAnswerObserver::class);
        MeasureApplication::observe(MeasureApplicationObserver::class);
        CustomMeasureApplication::observe(CustomMeasureApplicationObserver::class);
        CooperationMeasureApplication::observe(CooperationMeasureApplicationObserver::class);

        //View::creator('cooperation.my-account.*', MyAccountComposer::class);

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
        $this->app->bind('Cooperation', function () {
            $cooperation = null;
            if (Session::has('cooperation')) {
                $c = Session::get('cooperation');
                $cooperation = \App\Helpers\Cache\Cooperation::find($c);
            }

            return $cooperation;
        });

        $this->app->bind('CooperationStyle', function () {
            $cooperationStyle = null;
            if (Session::has('cooperation')) {
                // we know this as we've cached it earlier
                $c = Session::get('cooperation');

                $cooperationStyle = \App\Helpers\Cache\Cooperation::getStyle($c);
            }

            return $cooperationStyle;
        });
    }
}
