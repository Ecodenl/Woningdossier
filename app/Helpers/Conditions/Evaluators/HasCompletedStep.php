<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\InputSource;
use App\Models\Step;
use Illuminate\Support\Collection;

class HasCompletedStep extends ShouldEvaluate
{
    public function evaluate($value = null, ?Collection $answers = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;

        // This evaluator checks if the user has completed one or more steps, for one or more input sources.
        // $value must be an array, where
        // 'step' => one or more step shorts,
        // 'input_source' => one or more input sources,
        // 'should_pass' => whether or not this should pass; if set to false, the evaluation will be true if no steps
        // are completed

        $shouldPass = $value['should_pass'] ?? true;

        if (! is_null($this->override)) {
            $hasCompleted = $this->override;
            return [
                'results' => $hasCompleted,
                'bool' => $shouldPass ? $hasCompleted : ! $hasCompleted,
            ];
        }

        $steps = Step::findByShorts($value['steps']);
        $inputSources = InputSource::findByShorts($value['input_source_shorts']);

        $hasCompleted = $building->completedSteps()->allInputSources()
            ->whereIn('step_id', $steps->pluck('id')->toArray())
            ->whereIn('input_source_id', $inputSources->pluck('id')->toArray())
            ->count() > 0;

        return [
            'results' => $hasCompleted,
            'bool' => $shouldPass ? $hasCompleted : ! $hasCompleted,
        ];
    }
}