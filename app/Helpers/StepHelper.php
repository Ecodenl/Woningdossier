<?php

namespace App\Helpers;

use App\Events\StepDataHasBeenChanged;
use App\Models\Building;
use App\Models\CompletedStep;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\StepComment;
use App\Models\SubStep;

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

    const SERVICE_TO_SHORT = [
        'hr-boiler' => 'high-efficiency-boiler',
        'boiler' => 'high-efficiency-boiler',
        'total-sun-panels' => 'solar-panels',
        'sun-boiler' => 'heater',
        'house-ventilation' => 'ventilation',
    ];

    const QUICK_SCAN_STEP_SHORTS = [
        'building-data',
        'usage-quick-scan',
        'living-requirements',
        'residential-status',
        'small-measures'
    ];

    const STEP_COMPLETION_MAP = [
        'heating' => [
            'high-efficiency-boiler', 'heat-pump', 'heater',
        ],
    ];

    /**
     * Get all the comments categorized under step and input source.
     *
     * @param  \App\Models\Building  $building
     * @param $specificInputSource
     *
     * @return array
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
     * Get the next expert step. By default it's a redirect to my plan. If there's a questionnaire, however, we'll
     * go there first.
     *
     * @param  \App\Models\Step  $currentStep
     * @param  \App\Models\Questionnaire|null  $currentQuestionnaire
     *
     * @return string
     */
    public static function getNextExpertStep(Step $currentStep, Questionnaire $currentQuestionnaire = null): string
    {
        $url = route('cooperation.frontend.tool.expert-scan.index', ['step' => $currentStep]);

        // try to redirect them to a questionnaire that may exist on the step.
        if ($currentStep->hasActiveQuestionnaires()) {
            // There are active questionnaires. We just grab the first next questionnaire.
            $query = $currentStep->questionnaires()
                ->active()
                ->orderBy('order');

            if ($currentQuestionnaire instanceof Questionnaire) {
                // If we're currently on a questionnaire, we grab the next one
                $query->where('order', '>', $currentQuestionnaire->order);
            }

            $nextQuestionnaire = $query->first();

            if ($nextQuestionnaire instanceof Questionnaire) {
                // Next questionnaire exists, let's redirect to there
                return "{$url}#questionnaire-{$nextQuestionnaire->id}";
            }
        }

        // Redirect to my plan.
        return route('cooperation.frontend.tool.simple-scan.my-plan.index');
    }

    /**
     * Complete a step for a building.
     *
     * @param  \App\Models\Step  $step
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     *
     * @return void
     */
    public static function complete(Step $step, Building $building, InputSource $inputSource)
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
     * @param  \App\Models\Step  $step
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     *
     * @return void
     * @throws \Exception
     */
    public static function incomplete(Step $step, Building $building, InputSource $inputSource)
    {
        optional(CompletedStep::allInputSources()->where([
            'step_id' => $step->id,
            'input_source_id' => $inputSource->id,
            'building_id' => $building->id,
        ])->first())->delete();
    }

    /**
     * @param \App\Models\Step $step
     * @param \App\Models\Building $building
     * @param \App\Models\InputSource $inputSource
     * @param bool $triggerRecalculate
     *
     * @return bool True if the step can be completed, false if it can't be completed.
     */
    public static function completeStepIfNeeded(Step $step, Building $building, InputSource $inputSource, bool $triggerRecalculate): bool
    {
        $scan = $step->scan;
        \Log::debug("COMPLETE IF NEEDED {$step->short}");
        $allCompletedSubStepIds = CompletedSubStep::forInputSource($inputSource)
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

            // Trigger a recalculate if the tool is now complete
            // TODO: Refactor this
            if ($triggerRecalculate && $building->hasCompletedScan($scan, $inputSource)) {
                StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());
            }

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

                // Trigger a recalculate if the tool is now complete
                // TODO: Refactor this
                if ($triggerRecalculate && $building->hasCompletedScan($scan, $inputSource)) {
                    StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());
                }

                return true;
            }
        }

        return false;
    }
}
