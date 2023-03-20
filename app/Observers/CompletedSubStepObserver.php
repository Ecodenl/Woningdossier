<?php

namespace App\Observers;

use App\Events\BuildingCompletedHisFirstSubStep;
use App\Helpers\Queue;
use App\Jobs\CompleteRelatedSubStep;
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

            $scan = $step->scan;
            $scanRelatedSubStepIds = $scan->subSteps->pluck('id');

            $otherCompletedSubStepsForScan = $building
                ->completedSubSteps()
                ->forInputSource($inputSource)
                ->whereIn('sub_step_id', $scanRelatedSubStepIds)
                ->where('sub_step_id', '!=', 35)
                ->count();

            // so the sub step thats completed right now is the first one
            // the first progress has been made, so we will notify Econobis.
            if($otherCompletedSubStepsForScan === 0) {
                BuildingCompletedHisFirstSubStep::dispatch($building);
            }

            if ($step instanceof Step && $inputSource instanceof InputSource && $building instanceof Building) {
                // Master is handled by GetMyValuesTrait
                if ($inputSource->short !== InputSource::MASTER_SHORT) {
                    StepHelper::completeStepIfNeeded($step, $building, $inputSource, $building->user);
                    CompleteRelatedSubStep::dispatch($subStep, $building, $inputSource)->onQueue(Queue::ASYNC);
                }
            }
        }
    }
}
