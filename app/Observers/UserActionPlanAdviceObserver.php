<?php

namespace App\Observers;

use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;

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
    }
}
