<?php

namespace App\Observers;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\Cooperation\Tool\HeatPumpHelper;
use App\Helpers\Wrapper;
use App\Jobs\MapQuickScanSituationToExpert;
use App\Models\CooperationMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\SubStep;
use App\Models\UserActionPlanAdvice;
use App\Services\ConditionService;
use App\Services\Models\UserCostService;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UserActionPlanAdviceObserver
{
    /**
     * Listen to the creating event, will set the planned year based on interest.
     */
    public function creating(UserActionPlanAdvice $userActionPlanAdvice)
    {
        // previously custom logic decided if the advice should be planned or not.
        // since the "quick scan" we ask the user if he considers the measure, when he considers it an advice will be created
        // when he considers it it might as well be planned.
        $userActionPlanAdvice->planned = true;

        if (! $userActionPlanAdvice->isDirty('visible')) {
            // Visibility isn't set. Let's define it
            UserActionPlanAdviceService::setAdviceVisibility($userActionPlanAdvice);
        }
        if (! $userActionPlanAdvice->isDirty('category') || is_null($userActionPlanAdvice->category)) {
            // Category isn't set. Let's define it.
            UserActionPlanAdviceService::setAdviceCategory($userActionPlanAdvice);
        }

        if ($userActionPlanAdvice->inputSource->short !== InputSource::MASTER_SHORT) {
            $advisable = $userActionPlanAdvice->userActionPlanAdvisable()->withoutGlobalScopes()->first();
            if ($advisable instanceof MeasureApplication && in_array($advisable->short, array_keys(HeatPumpHelper::MEASURE_SERVICE_LINK))) {
                $building = $userActionPlanAdvice->user->building;
                if (! ConditionService::init()->building($building)->inputSource($userActionPlanAdvice->inputSource)->hasCompletedSteps(['heating'])) {
                    // User has not yet completed the expert. We will map values, then do a new calculation as
                    // values might no longer match. Due to dispatchSync the "recalc" only happens after the mapping.
                    MapQuickScanSituationToExpert::dispatchSync(
                        $building,
                        $userActionPlanAdvice->inputSource,
                        $advisable
                    );
                    $heatPumpHelper = HeatPumpHelper::init($building->user, $userActionPlanAdvice->inputSource)
                        ->createValues();
                    $evaluator = ConditionEvaluator::init()
                        ->building($building)
                        ->inputSource($userActionPlanAdvice->inputSource);

                    $currentCalculateValue = null;
                    $heatPumpSubStep = SubStep::bySlug('warmtepomp')->first();
                    if ($evaluator->evaluate($heatPumpSubStep->conditions)) {
                        $currentCalculateValue = $heatPumpHelper->getCurrentHeatPump();
                    }

                    $results = $heatPumpHelper->getResults($advisable->short, $currentCalculateValue);

                    $userActionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results['cost_indication']);
                    $userActionPlanAdvice->savings_money = $results['savings_money'];
                }
            }

            // Check if we should apply user costs
            if ($advisable instanceof Model) {
                $cost = UserCostService::init()->user($userActionPlanAdvice->user)
                    ->inputSource($userActionPlanAdvice->inputSource)
                    ->forAdvisable($advisable)
                    ->getCost();

                if (! is_null($cost)) {
                    $userActionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($cost);
                }
            }
        }
    }

    public function created(UserActionPlanAdvice $userActionPlanAdvice)
    {
        // Triggered from frontend (Woonplan or step), you need it directly. There is no choice to queue it here.
        // Or its triggered from a recalculation, which means the code is already running on a queue.
        // usually we would exclude the master input source, however the refreshRegulations does NOT trigger model events.
        // We don't do it for the custom measure here, as creating a custom measure will apply its own refresh call.
        if (in_array($userActionPlanAdvice->user_action_plan_advisable_type, [MeasureApplication::class, CooperationMeasureApplication::class])) {
            Log::debug('Refresh ze regulations.');
            Wrapper::wrapCall(function () use ($userActionPlanAdvice) {
                UserActionPlanAdviceService::init()
                    ->forUser($userActionPlanAdvice->user)
                    ->refreshRegulations($userActionPlanAdvice);
            });
        }
    }
}
