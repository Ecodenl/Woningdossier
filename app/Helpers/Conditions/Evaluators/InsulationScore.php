<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HeatPump;
use Illuminate\Support\Collection;

class InsulationScore extends ShouldEvaluate
{
    public function evaluate($value = null, ?Collection $answers = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;

        // This evaluator checks if the user's insulation is good enough to install a heat pump.
        // $value is expected as float/int.

        $key = md5(json_encode([null]));

        if (! empty($this->override[$key])) {
            $results = $this->override;
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