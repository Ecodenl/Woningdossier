<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HighEfficiencyBoiler;
use Illuminate\Support\Collection;

class HrBoilerAdvice extends ShouldEvaluate
{
    public function evaluate($value = null, ?Collection $answers = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;

        // This evaluator checks if the boiler_advice is returned in the calculation. This advice tells the user
        // that they won't receive much efficiency improvement because they already have a high quality HR-boiler

        $key = md5(json_encode([null]));

        if (! empty($this->override[$key])) {
            $results = $this->override;
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