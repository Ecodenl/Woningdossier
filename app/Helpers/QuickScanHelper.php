<?php

namespace App\Helpers;

use App\Models\Step;
use App\Models\SubStep;

class QuickScanHelper
{
    /**
     * Method to return the next step url
     *
     * @param  Step  $step
     * @param  SubStep  $subStep
     *
     * @return string
     */
    public static function getNextStepUrl(Step $step, SubStep $subStep)
    {
        // TODO: Make this function with questionnaires?
        // TODO: Could this be merged within StepHelper::getNextStep ?
        $nextSubStep = $step->subSteps()
            ->where('order', '>', $subStep->order)
            ->orderBy('order')
            ->first();
        $nextStep = $step;

        // we will check if the current sub step is the last one, that way we know we have to go to the next one.
        $lastSubStepForStep = $step->subSteps()->orderByDesc('order')->first();
        if ($lastSubStepForStep->id === $subStep->id) {
            $nextStep = $step->nextQuickScan();
            // the last can't have a next one
            if ($nextStep instanceof Step) {
                // the previous step is a different one, so we should get the first sub step of the previous step
                $nextSubStep = $nextStep->subSteps()->first();
            }
        }

        // A step is set, but a next sub step is not, let's check something else
        // User could have stopped filling data during a step, and therefore the last completed step
        // is not the current step for the sub step
        if ($nextStep instanceof Step && ! $nextSubStep instanceof SubStep && $nextStep->id !== $subStep->id) {
            $nextStep = $subStep->step;
            $nextSubStep = $subStep;
        }

        // this will happen when the user completed each step, so we will redirect him to the action plan
        if (! $nextStep instanceof Step && ! $nextSubStep instanceof SubStep) {
            return route('cooperation.frontend.tool.quick-scan.index', compact(
                'step', 'subStep',
            ));
        }

        return route('cooperation.frontend.tool.quick-scan.index', [
            'step' => $nextStep, 'subStep' => $nextSubStep,
        ]);
    }
}