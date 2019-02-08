<?php

namespace App\Observers;

use App\Helpers\HoomdossierSession;
use App\Models\UserActionPlanAdvice;

class UserActionPlanAdviceObserver
{
    /**
     * Listen to the creating event, we need to set the input_source_id on every creating event.
     *
     * @param UserActionPlanAdvice $userActionPlanAdvice
     */
    public function creating(UserActionPlanAdvice $userActionPlanAdvice)
    {
        $userActionPlanAdvice->input_source_id = HoomdossierSession::getInputSource();
    }
}
