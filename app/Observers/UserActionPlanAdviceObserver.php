<?php

namespace App\Observers;

use App\Models\MeasureApplication;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
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
        $buildingOwner = $userActionPlanAdvice->user;
        $step = $userActionPlanAdvice->step;
        $userActionPlanAdvisable = $userActionPlanAdvice->userActionPlanAdvisable;
        $inputSource = $userActionPlanAdvice->inputSource;
        $planned = false;

        if ($userActionPlanAdvisable instanceof MeasureApplication) {

            // set the default user interest on the step.
            $userInterest = $buildingOwner->userInterestsForSpecificType(get_class($step), $step->id, $inputSource)->with('interest')->first();
            // try to obtain a specific interest on the measure application
            $userInterestOnMeasureApplication = $buildingOwner
                ->userInterestsForSpecificType(get_class($userActionPlanAdvisable), $userActionPlanAdvisable->id, $inputSource)
                ->with('interest')
                ->first();

            // when  that's available: use that.
            if ($userInterestOnMeasureApplication instanceof UserInterest) {
                $userInterest = $userInterestOnMeasureApplication;
            }

            // this if is a safe haven
            if(!$userInterest instanceof Model) {
                $planned = false;
            } else if ($userInterest->interest->calculate_value <= 3) {
                $planned = true;
            }

            $userActionPlanAdvice->planned = $planned;

            if (is_null($userActionPlanAdvice->year)) {
                $userActionPlanAdvice->year = UserActionPlanAdviceService::getAdviceYear($userActionPlanAdvice);
            }
        }

        if (! $userActionPlanAdvice->isDirty('visible')) {
            $userActionPlanAdvice->visible = true;
        }
        if (! $userActionPlanAdvice->isDirty('category') || is_null($userActionPlanAdvice->category)) {
            $userActionPlanAdvice->category = UserActionPlanAdviceService::CATEGORY_TO_DO;
        }
    }
}
