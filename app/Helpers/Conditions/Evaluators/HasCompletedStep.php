<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\Step;
use Illuminate\Support\Collection;

class HasCompletedStep implements ShouldEvaluate
{
    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        // This evaluator checks if the user has completed one or more steps, for one or more input sources.
        // $value must be an array, where
        // 'step' => one or more step shorts,
        // 'input_source' => zero or more input sources; if left empty, the passed $inputSource will be used,
        // 'should_pass' => whether or not this should pass; if set to false, the evaluation will be true if no steps
        // are completed

        $steps = Step::findByShorts((array) $value['steps']);
        $inputSources = InputSource::findByShorts(empty($value['input_source']) ? [$inputSource] : (array) $value['input_source']);
        $shouldPass = $value['should_pass'] ?? true;

        $hasCompleted = $building->completedSteps()->allInputSources()
            ->whereIn('step_id', $steps->pluck('id')->toArray())
            ->whereIn('input_source_id', $inputSources->pluck('id')->toArray())
            ->count() > 0;

        return $shouldPass ? $hasCompleted : ! $hasCompleted;
    }
}