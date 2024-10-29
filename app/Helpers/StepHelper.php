<?php

namespace App\Helpers;

use App\Events\StepDataHasBeenChanged;
use App\Models\Building;
use App\Models\CompletedStep;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\StepComment;
use App\Models\SubStep;
use App\Models\User;

class StepHelper
{
    const ELEMENT_TO_SHORT = [
        'sleeping-rooms-windows' => 'insulated-glazing',
        'living-rooms-windows' => 'insulated-glazing',
        'crack-sealing' => 'insulated-glazing',
        'wall-insulation' => 'wall-insulation',
        'floor-insulation' => 'floor-insulation',
        'roof-insulation' => 'roof-insulation',
    ];

    // TODO: Unused?
    const SERVICE_TO_SHORT = [
        'hr-boiler' => 'high-efficiency-boiler',
        'boiler' => 'high-efficiency-boiler',
        'total-sun-panels' => 'solar-panels',
        'sun-boiler' => 'heater',
        'house-ventilation' => 'ventilation',
    ];

    const STEP_COMPLETION_MAP = [
        'heating' => [
            'high-efficiency-boiler', 'heat-pump', 'heater',
        ],
    ];

    /**
     * Get all the comments categorized under step and input source.
     *
     * @param $specificInputSource
     */
    public static function getAllCommentsByStep(Building $building, $specificInputSource = null): array
    {
        $commentsByStep = [];

        $stepComments = StepComment::forMe($building->user)->with('step', 'inputSource')->get();

        // when set, we will only return the comments for the given input source
        if ($specificInputSource instanceof InputSource) {
            $stepComments = $stepComments->where('input_source_id', $specificInputSource->id);
        }

        foreach ($stepComments as $stepComment) {
            // General data is now hidden, so we must check if the step is set
            // If everything is mapped correctly, it will be set under the quick scan steps, but just in case...
            if (! is_null($stepComment->step)) {
                $commentsByStep[$stepComment->step->short][$stepComment->short ?? '-'][$stepComment->inputSource->name] = $stepComment->comment;
            }
        }

        return $commentsByStep;
    }

    /**
     * Complete a step for a building.
     */
    public static function complete(Step $step, Building $building, InputSource $inputSource): void
    {
        CompletedStep::allInputSources()->firstOrCreate([
            'step_id' => $step->id,
            'input_source_id' => $inputSource->id,
            'building_id' => $building->id,
        ]);

        // Also complete related steps (this is the case for steps that used to be separate but are now included
        // in a single step, but underlying code is still relying on them being completed separately)
        if (array_key_exists($step->short, static::STEP_COMPLETION_MAP)) {
            foreach (static::STEP_COMPLETION_MAP[$step->short] as $short) {
                $stepToComplete = Step::findByShort($short);
                CompletedStep::allInputSources()->firstOrCreate([
                    'step_id' => $stepToComplete->id,
                    'input_source_id' => $inputSource->id,
                    'building_id' => $building->id,
                ]);
            }
        }
    }

    /**
     * Incomplete a step for a building.
     *
     *
     * @throws \Exception
     */
    public static function incomplete(Step $step, Building $building, InputSource $inputSource): void
    {
        optional(CompletedStep::allInputSources()->where([
            'step_id' => $step->id,
            'input_source_id' => $inputSource->id,
            'building_id' => $building->id,
        ])->first())->delete();
    }

    /**
     *
     * @return bool True if the step can be completed, false if it can't be completed.
     */
    public static function completeStepIfNeeded(Step $step, Building $building, InputSource $inputSource, User $authUser): bool
    {
        // We want to check if the user has completed the sub steps on master. The sub steps might be completed
        // in a mixed bag of coach and resident.
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $allCompletedSubStepIds = CompletedSubStep::forInputSource($masterInputSource)
            ->forBuilding($building)
            ->whereHas('subStep', function ($query) use ($step) {
                $query->where('step_id', $step->id);
            })
            ->pluck('sub_step_id')->toArray();

        $allSubStepIds = $step->subSteps()->pluck('id')->toArray();

        $diff = array_diff($allSubStepIds, $allCompletedSubStepIds);

        if (empty($diff)) {
            // The sub step that has been completed finished up the set, so we complete the main step
            static::complete($step, $building, $inputSource);

            StepDataHasBeenChanged::dispatch($step, $building, $authUser, $inputSource);
            return true;
        } else {
            // We didn't fill in each sub step. But, it might be that there's sub steps with conditions
            // that we didn't get. Let's check
            $leftoverSubSteps = SubStep::findMany($diff);

            $cantSee = 0;
            foreach ($leftoverSubSteps as $subStep) {
                if (! $building->user->account->can('show', [$subStep, $building])) {
                    ++$cantSee;
                }
            }

            if ($cantSee === $leftoverSubSteps->count()) {
                // Conditions "passed", so we complete!
                static::complete($step, $building, $inputSource);

                StepDataHasBeenChanged::dispatch($step, $building, $authUser, $inputSource);
                return true;
            }
        }

        return false;
    }
}
