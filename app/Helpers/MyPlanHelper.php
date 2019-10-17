<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\Interest;
use App\Models\Log;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyPlanHelper
{
    /**
     * Save a user's interests from the my plan page.
     *
     * @param UserActionPlanAdvice $advice
     * @param array $newAdviceSaveData
     * @return array
     */
    public static function saveUserInterests(UserActionPlanAdvice $advice, array $newAdviceSaveData): array
    {
        $stepSlug = $advice->step->slug;

        // the planned year input
        $requestPlannedYear = null;
        // the interested checkbox, which fills the planned column in the table
        $interested = false;

        if (array_key_exists('planned_year', $newAdviceSaveData[$stepSlug])) {
            $requestPlannedYear = $newAdviceSaveData[$stepSlug]['planned_year'];
        }

        if (array_key_exists('interested', $newAdviceSaveData[$stepSlug])) {
            $interested = true;
        }

        // update the planned year
        $updates = [
            'planned' => $interested,
            'planned_year' => $requestPlannedYear,
        ];

        // update the advices
        $advice->update($updates);

        return $newAdviceSaveData;
    }
}
