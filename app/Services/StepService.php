<?php

namespace App\Services;

use App\Models\Step;

class StepService {

    public static function hasCompleted(Step $step)
    {
        if ($step->isSubStep()) {

        } else {
            // todo: check if all the sub steps / children are done. If so the step is completed.
            // note: sub steps can be disabled
        }
    }
}