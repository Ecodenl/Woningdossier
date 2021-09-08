<?php

namespace App\Helpers;

use App\Models\UserActionPlanAdvice;

class MyPlanHelper
{
    /**
     * Save a user's interests from the my plan page.
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
            // Sometimes a user will fill in a "?" for the planned year.
            // We can't save this, so we will set it to null.
            // We could add this in a form request but then the
            // whole request won't happen and the personal plan won't load.
            'planned_year' => is_numeric($requestPlannedYear) ? $requestPlannedYear : null,
        ];

        // update the advices
        $advice->update($updates);

        return $newAdviceSaveData;
    }
}
