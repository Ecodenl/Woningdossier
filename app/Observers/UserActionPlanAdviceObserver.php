<?php

namespace App\Observers;

use App\Helpers\HoomdossierSession;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Services\UserActionPlanAdviceService;

class UserActionPlanAdviceObserver
{
    /**
     * Listen to the creating event, will set the planned year based on interest.
     */
    public function creating(UserActionPlanAdvice $userActionPlanAdvice)
    {
        $buildingOwner = $userActionPlanAdvice->user;
        $step = $userActionPlanAdvice->step;
        $measureApplication = $userActionPlanAdvice->measureApplication;
        $inputSource = $userActionPlanAdvice->inputSource;
        $planned = false;

        // set the default user interest on the step.
        $userInterest = $buildingOwner->userInterestsForSpecificType(get_class($step), $step->id, $inputSource)->with('interest')->first();
        // try to obtain a specific interest on the measure application
        $userInterestOnMeasureApplication = $buildingOwner
            ->userInterestsForSpecificType(get_class($measureApplication), $measureApplication->id, $inputSource)
            ->with('interest')
            ->first();

        // when thats available use that.
        if ($userInterestOnMeasureApplication instanceof UserInterest) {
            $userInterest = $userInterestOnMeasureApplication;
        }

        if (!$userInterest instanceof UserInterest) {
            dd($userInterest, $userActionPlanAdvice->user_id);
        }
        // Ja op korte termijn, ja op termijn and more informatie
        if ($userInterest->interest->calculate_value <= 3) {
            $planned = true;
        }

        $userActionPlanAdvice->planned = $planned;

        if (is_null($userActionPlanAdvice->year)) {
            $userActionPlanAdvice->year = UserActionPlanAdviceService::getAdviceYear($userActionPlanAdvice);
        }
    }
}
