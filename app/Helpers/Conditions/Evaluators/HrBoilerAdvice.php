<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HighEfficiencyBoiler;

class HrBoilerAdvice extends ShouldEvaluate
{
    public function evaluate($value = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;
        $answers = $this->answers;

        // This evaluator checks if the boiler_advice is returned in the calculation. This advice tells the user
        // that they won't receive much efficiency improvement because they already have a high quality HR-boiler

        $key = md5(json_encode([null]));

        if (array_key_exists($key, $this->override)) {
            $results = $this->override[$key];
            return [
                'results' => $results,
                'bool' => array_key_exists('boiler_advice', $results),
                'key' => $key,
            ];
        }

        $results = HighEfficiencyBoiler::calculate($building, $inputSource, $answers);

        return [
            'results' => $results,
            'bool' => array_key_exists('boiler_advice', $results),
            'key' => $key,
        ];
    }
}
