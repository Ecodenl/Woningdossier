<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\CompletedStep;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\StepComment;

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
        $url = route("cooperation.tool.{$currentStep->short}.index");

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
        return route('cooperation.frontend.tool.quick-scan.my-plan.index');
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
    }
}
