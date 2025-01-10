<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HeatPump;

class InsulationScore extends ShouldEvaluate
{
    public function evaluate($value = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;
        $answers = $this->answers;

        // This evaluator checks if the user's insulation is good enough to install a heat pump.
        // $value is expected as float/int.

        $key = md5(json_encode([null]));

        if (array_key_exists($key, $this->override)) {
            $results = $this->override[$key];
            return [
                'results' => $results,
                'bool' => $results < $value,
                'key' => $key,
            ];
        }

        $results = HeatPump::init($building, $inputSource, $answers)->insulationScore();

        return [
            'results' => $results,
            'bool' => $results < $value,
            'key' => $key,
        ];
    }
}
