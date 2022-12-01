<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\Building;
use App\Models\InputSource;
use App\Traits\HasDynamicAnswers;
use Illuminate\Support\Collection;

class HasMaximumAnswers implements ShouldEvaluate
{
    use HasDynamicAnswers;

    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        // This evaluator checks if the given answers doesn't exceed maximum
        // $value must be an array, where
        // 'column' => The tool question short to check
        // 'max' => Max amount of answers
        // 'ignore' => Potential answers to ignore (applicable for array)

        $answer = static::getQuickAnswer($value['column'], $building, $inputSource, $answers);

        // TODO: Make this work with INT/STRING etc.
        if (is_array($answer)) {
            $ignores = (array) ($value['ignore'] ?? []);
            foreach ($ignores as $ignore) {
                $index = array_search($ignore, $answer);

                if ($index !== false) {
                    unset($answer[$index]);
                }
            }

            return count($answer) < $value['max'];
        }

        return false;
    }
}