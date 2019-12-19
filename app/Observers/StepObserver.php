<?php

namespace App\Observers;

use App\Models\Cooperation;
use App\Models\Step;

class StepObserver {

    /**
     * When a new step is created add them to all the cooperations.
     *
     * @param Step $step
     */
    public function created(Step $step)
    {
        foreach (Cooperation::all() as $cooperation) {
            $cooperationStepsQuery = $cooperation->steps();
            $cooperationStepsQuery->attach($step->id);
            $cooperationStep = $cooperationStepsQuery->find($step->id);
            $cooperationStepsQuery->updateExistingPivot($cooperationStep->id, ['order' => $step->order]);
        }
    }
}