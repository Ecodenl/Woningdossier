<?php

namespace App\Observers;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\Cooperation\Tool\HeatPumpHelper;
use App\Jobs\MapQuickScanSituationToExpert;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\SubStep;
use App\Models\UserActionPlanAdvice;
use App\Services\ConditionService;
use App\Services\UserActionPlanAdviceService;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Support\Facades\Log;

class UserActionPlanAdviceObserver
{
    public function saving(UserActionPlanAdvice $userActionPlanAdvice)
    {
        // default on false, ofcourse.
        $userActionPlanAdvice->fill([
            'loan_available' => false,
            'subsidy_available' => false,
        ]);

        if ($userActionPlanAdvice->userActionPlanAdvisable instanceof MeasureApplication) {
            Log::debug("----SUBSIDY {$userActionPlanAdvice->userActionPlanAdvisable->measure_name} ----");
            $payload = RegulationService::init()
                ->forBuilding($userActionPlanAdvice->user->building)
                ->get();

            $regulations = $payload
                ->forMeasureApplication($userActionPlanAdvice->userActionPlanAdvisable)
                ->forBuildingContractType($userActionPlanAdvice->user->building, $userActionPlanAdvice->inputSource);

            if ($regulations->getLoans()->isNotEmpty()) {
                Log::debug("Is loan");
                $userActionPlanAdvice->loan_available = true;
            }
            if ($regulations->getSubsidies()->isNotEmpty()) {
                Log::debug("Is subsidy");
                $userActionPlanAdvice->subsidy_available = true;
            }
        }
    }

    /**
     * Listen to the creating event, will set the planned year based on interest.
     */
    public function creating(UserActionPlanAdvice $userActionPlanAdvice)
    {
        // previously custom logic decided if the advice should be planned or not.
        // since the "quick scan" we ask the user if he considers the measure, when he considers it an advice will be created
        // when he considers it it might as well be planned.
        $userActionPlanAdvice->planned = true;

        if ( ! $userActionPlanAdvice->isDirty('visible')) {
            // Visibility isn't set. Let's define it
            UserActionPlanAdviceService::setAdviceVisibility($userActionPlanAdvice);
        }
        if ( ! $userActionPlanAdvice->isDirty('category') || is_null($userActionPlanAdvice->category)) {
            // Category isn't set. Let's define it.

            UserActionPlanAdviceService::setAdviceCategory($userActionPlanAdvice);
        }


        if ($userActionPlanAdvice->inputSource->short !== InputSource::MASTER_SHORT) {
            $advisable = $userActionPlanAdvice->userActionPlanAdvisable;
            if ($advisable instanceof MeasureApplication && in_array($advisable->short,
                    array_keys(HeatPumpHelper::MEASURE_SERVICE_LINK))) {
                $building = $userActionPlanAdvice->user->building;
                if ( ! ConditionService::init()->building($building)->inputSource($userActionPlanAdvice->inputSource)->hasCompletedSteps(['heating'])) {
                    // User has not yet completed the expert. We will map values, then do a new calculation as
                    // values might no longer match. Due to dispatchSync the "recalc" only happens after the mapping.
                    MapQuickScanSituationToExpert::dispatchSync($building, $userActionPlanAdvice->inputSource,
                        $advisable);
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
        }
    }
}
