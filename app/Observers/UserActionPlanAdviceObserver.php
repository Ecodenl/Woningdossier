<?php

namespace App\Observers;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Services\UserActionPlanAdviceService;

class UserActionPlanAdviceObserver
{
    /**
     * Listen to the creating event, we need to set the input_source_id on every creating event.
     *
     * @param UserActionPlanAdvice $userActionPlanAdvice
     */
    public function creating(UserActionPlanAdvice $userActionPlanAdvice)
    {
        $buildingOwner = $userActionPlanAdvice->user;
        $step = $userActionPlanAdvice->step;
        $measureApplication = $userActionPlanAdvice->measureApplication;
        $planned = false;

        // set the default user interest on the step.
        $userInterest = $buildingOwner->userInterestsForSpecificType(get_class($step), $step->id)->with('interest')->first();
        // try to obtain a specific interest on the measure application
        $userInterestOnMeasureApplication = $buildingOwner
            ->userInterestsForSpecificType(get_class($measureApplication), $measureApplication->id)
            ->with('interest')
            ->first();

        // when thats available use that.
        if ($userInterestOnMeasureApplication instanceof UserInterest) {
            $userInterest = $userInterestOnMeasureApplication;
        }

        // Ja op korte termijn, ja op termijn and more informatie
        if ($userInterest->interest->calculate_value <= 3 ) {
            $planned = true;
        }

        $userActionPlanAdvice->input_source_id = HoomdossierSession::getInputSource();
        $userActionPlanAdvice->planned = $planned;

        if (is_null($userActionPlanAdvice->year)) {
            $userActionPlanAdvice->year = UserActionPlanAdviceService::getAdviceYear($userActionPlanAdvice);
        }
    }
}
