<?php

namespace App\Helpers;

use App\Models\Step;
use App\Models\SubStep;

class QuickScanHelper
{

    /**
     * Method to return the next step url
     *
     * @param Step $step
     * @param SubStep $subStep
     * @return string
     */
    public static function getNextStepUrl(Step $step, SubStep $subStep)
    {
        $nextSubStep = $step->subSteps()->where('order', '>', $subStep->order)->orderBy('order')->first();
        $nextStep = $step;
        // we will check if the current sub step is the last one, that way we know we have to go to the next one.
        $lastSubStepForStep = $step->subSteps()->orderByDesc('order')->first();
        if ($lastSubStepForStep->id === $subStep->id) {
            $nextStep = $step->nextQuickScan();
            // the last cant have a next one
            if ($nextStep instanceof Step) {
                // the previous step is a different one, so we should get the first sub step of the previous step
                $nextSubStep = $nextStep->subSteps()->first();
            }
        }

        // this will happen when the user completed each step, so we will redirect him to the action plan
        if (!$nextStep instanceof Step && !$nextSubStep instanceof SubStep) {
            // todo: when housing plan bracnh is updated change this to the correct route.
            return route('cooperation.frontend.tool.quick-scan.index', compact('step', 'subStep'));
        }


        return route('cooperation.frontend.tool.quick-scan.index', ['step' => $nextStep, 'subStep' => $nextSubStep]);
    }
}