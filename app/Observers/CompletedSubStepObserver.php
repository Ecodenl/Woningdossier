<?php

namespace App\Observers;

use App\Helpers\Hoomdossier;
use App\Models\CompletedSubStep;
use App\Events\StepDataHasBeenChanged;
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
                $allCompletedSubStepIds = CompletedSubStep::forInputSource($inputSource)
                    ->forBuilding($building)
                    ->whereHas('subStep', function ($query) use ($step) {
                        $query->where('step_id', $step->id);
                    })
                    ->pluck('sub_step_id')->toArray();

                $allSubStepIds = $step->subSteps()->pluck('id')->toArray();

                $diff = array_diff($allSubStepIds, $allCompletedSubStepIds);

                if (empty ($diff)) {
                    // The sub step that has been completed finished up the set, so we complete the main step
                    StepHelper::complete($step, $building, $inputSource);

                    // Trigger a recalculate if the tool is now complete
                    // TODO: Refactor this
                    if ($building->hasCompletedQuickScan($inputSource)) {
                        StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());
                    }
                } else {
                    // We didn't fill in each sub step. But, it might be that there's sub steps with conditions
                    // that we didn't get. Let's check
                    $leftoverSubSteps = SubStep::findMany($diff);

                    $cantSee = 0;
                    foreach ($leftoverSubSteps as $subStep) {
                        if (! $building->user->account->can('show', $subStep)) {
                            ++$cantSee;
                        }
                    }

                    if ($cantSee === $leftoverSubSteps->count()) {
                        // Conditions "passed", so we complete!
                        StepHelper::complete($step, $building, $inputSource);

                        // Trigger a recalculate if the tool is now complete
                        // TODO: Refactor this
                        if ($building->hasCompletedQuickScan($inputSource)) {
                            StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());
                        }
                    }
                }
            }
        }
    }
}
