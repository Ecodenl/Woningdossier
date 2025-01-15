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
use App\Observers\UserActionPlanAdviceObserver;
use App\Observers\UserObserver;
use App\Models\ToolQuestionAnswer;
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
        Cooperation::observe(CooperationObserver::class);
        PrivateMessage::observe(PrivateMessageObserver::class);
        UserActionPlanAdvice::observe(UserActionPlanAdviceObserver::class);
        PrivateMessageView::observe(PrivateMessageViewObserver::class);
        Building::observe(BuildingObserver::class);
        BuildingFeature::observe(BuildingFeatureObserver::class);
        BuildingElement::observe(BuildingElementObserver::class);
        User::observe(UserObserver::class);
        Account::observe(AccountObserver::class);
        CompletedSubStep::observe(CompletedSubStepObserver::class);
        ToolQuestionAnswer::observe(ToolQuestionAnswerObserver::class);
        MeasureApplication::observe(MeasureApplicationObserver::class);
        CustomMeasureApplication::observe(CustomMeasureApplicationObserver::class);
        CooperationMeasureApplication::observe(CooperationMeasureApplicationObserver::class);

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
