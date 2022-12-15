<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use Illuminate\Support\Collection;

class SubStepHelper
{
    public static function getIncompleteSubSteps(Building $building, Step $step, InputSource $inputSource): Collection
    {
        // the completed steps, so the ones we do not want.
        $irrelevantSubSteps = $building->completedSubSteps()->forInputSource($inputSource)->pluck('sub_step_id')->toArray();

        return $step->subSteps()
            ->whereNotIn('id', $irrelevantSubSteps)
            ->orderBy('order')
            ->get();
    }
}
