<?php

namespace App\Observers;

use App\Models\CompletedSubStep;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;

class CompletedSubStepObserver
{
    public function saved(CompletedSubStep $completedSubStep)
    {
        // Check if this sub step finished the step
        $subStep = $completedSubStep->subStep;

        if ($subStep instanceof SubStep) {
            $step = $subStep->step;
            $inputSource = $completedSubStep->inputSource;
            $building = $completedSubStep->building;

            if ($step instanceof Step && $inputSource instanceof InputSource && $building instanceof Building) {
                StepHelper::completeStepIfNeeded($step, $building, $inputSource, true);
            }
        }
    }
}
