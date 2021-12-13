<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\CompletedStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\StepComment;
use App\Models\User;
use App\Models\UserInterest;
use Illuminate\Database\Query\Builder;

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
    ];

    /**
     * Get all the comments categorized under step and input source.
     *
     * @param bool $withEmptyComments
     * @param null $specificInputSource
     */
    public static function getAllCommentsByStep(
        Building $building,
        $withEmptyComments = false,
        $specificInputSource = null
    ): array {
        $commentsByStep = [];

        if (! $building instanceof Building) {
            return [];
        }

        $stepComments = StepComment::forMe($building->user)->with('step',
            'inputSource')->get();

        // when set, we will only return the comments for the given input source
        if ($specificInputSource instanceof InputSource) {
            $stepComments = $stepComments->where('input_source_id',
                $specificInputSource->id);
        }

        foreach ($stepComments as $stepComment) {
            // General data is now hidden, so we must check if the step is set
            // If everything is mapped correctly, it will be set under the quick scan steps, but just in case...
            if (! is_null($stepComment->step)) {
                if ($stepComment->step->isChild()) {
                    if (is_null($stepComment->short)) {
                        $commentsByStep[$stepComment->step->parentStep->short][$stepComment->step->short][$stepComment->inputSource->name] = $stepComment->comment;
                    } else {
                        $commentsByStep[$stepComment->step->parentStep->short][$stepComment->step->short][$stepComment->inputSource->name][$stepComment->short] = $stepComment->comment;
                    }
                } else {
                    if (is_null($stepComment->short)) {
                        $commentsByStep[$stepComment->step->short]['-'][$stepComment->inputSource->name] = $stepComment->comment;
                    }
                }
            }
        }

        return $commentsByStep;
    }

    /**
     * Method to retrieve the unfinished sub steps of a step for a building.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public static function getUnfinishedChildrenForStep(
        Step $step,
        Building $building,
        InputSource $inputSource
    ) {
        return $step->children()
            ->whereNotExists(function (Builder $query) use (
                $building,
                $inputSource
            ) {
                $query->select('*')
                    ->from('completed_steps')
                    ->where('completed_steps.input_source_id',
                        $inputSource->id)
                    ->whereRaw('steps.id = completed_steps.step_id')
                    ->where('building_id', $building->id);
            })->get();
    }

    /**
     * Get the next step for a user where the user shows interest in or the next questionnaire for a user.
     *
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     * @param  \App\Models\Step  $current
     * @param  \App\Models\Questionnaire|null  $currentQuestionnaire
     *
     * @return array
     */
    public static function getNextStep(
        Building $building,
        InputSource $inputSource,
        Step $current,
        Questionnaire $currentQuestionnaire = null
    ): array {
        /**
         * some default stuff set, so we don't get if else in this method all over the place.
         *
         * @var Step $parentStep
         * @var Step $subStep
         */
        $parentStep = $current->isChild() ? $current->parentStep : $current;
        $subStep = $current->isChild() ? $current : null;

        $url = static::buildStepUrl($parentStep, $subStep);

        $user = $building->user;

        $nonCompletedSteps = collect();
        // when there is a sub step, try to redirect them to the next sub step
        if ($subStep instanceof Step) {
            $nonCompletedSteps = static::getUnfinishedChildrenForStep($parentStep,
                $building, $inputSource);
        }

        // there are no uncompleted sub steps for the parent, try to redirect them to a questionnaire that may exist
        // on the parent step (child steps cannot have questionnaires).
        if ($nonCompletedSteps->isEmpty() && $parentStep->hasActiveQuestionnaires()) {
            // There are active questionnaires. We just grab the first next questionnaire.
            $query =  $parentStep->questionnaires()
                ->active()
                ->orderBy('order');

            if ($currentQuestionnaire instanceof Questionnaire) {
                // If we're currently on a questionnaire, we grab the next one
                $query->where('order', '>', $currentQuestionnaire->order);
            }

            $nextQuestionnaire = $query->first();

            if ($nextQuestionnaire instanceof Questionnaire) {
                // Next questionnaire exists, let's redirect to there
                return [
                    'url' => $url,
                    'tab_id' => 'questionnaire-'.$nextQuestionnaire->id,
                ];
            }
        }

        foreach ($nonCompletedSteps as $nonCompletedStep) {
            // when the non completed step is a sub step, we can always return it.
            // else we have to check whether the user has interest in the step
            if ($nonCompletedStep instanceof Step && $nonCompletedStep->isChild()) {
                // when it's a sub step we need to build it again for the sub step
                if ($nonCompletedStep->isChild()) {
                    $url = static::buildStepUrl($parentStep, $nonCompletedStep);
                } else {
                    $url = static::buildStepUrl($nonCompletedStep);
                }

                return ['url' => $url, 'tab_id' => ''];
            }
        }

        // if the user has no steps left where they do not have any interest in, redirect them to their plan
        return [
            'url' => route('cooperation.frontend.tool.quick-scan.my-plan.index'),
            'tab_id' => '',
        ];
    }

    /**
     * Return a step url.
     *
     * @param null $subStep
     */
    public static function buildStepUrl(Step $parentStep, $subStep = null): string
    {
        return route(
            $subStep instanceof Step ? 'cooperation.tool.'.$parentStep->short.'.'.$subStep->short.'.index' : 'cooperation.tool.'.$parentStep->short.'.index'
        );
    }

    /**
     * Complete a step for a building.
     */
    public static function complete(Step $step, Building $building, InputSource $inputSource)
    {
        CompletedStep::allInputSources()->firstOrCreate([
            'step_id' => $step->id,
            'input_source_id' => $inputSource->id,
            'building_id' => $building->id,
        ]);

        // check if all sub steps are completed, if so complete the parent step
        if ($step->isChild()) {
            $parentStep = $step->parentStep;
            $uncompletedSubStepsForParentStep = $parentStep
                ->children()
                ->whereNotExists(function (Builder $query) use ($building, $inputSource) {
                    $query->select('*')
                        ->from('completed_steps')
                        ->whereRaw('steps.id = completed_steps.step_id')
                        ->where('building_id', $building->id)
                        ->where('input_source_id', $inputSource->id);
                })->get();

            // when there are no uncompleted sub steps, we can complete the parent step.
            if ($uncompletedSubStepsForParentStep->isEmpty()) {
                CompletedStep::firstOrCreate([
                    'step_id' => $parentStep->id,
                    'input_source_id' => $inputSource->id,
                    'building_id' => $building->id,
                ]);
            }
        }
    }
}
