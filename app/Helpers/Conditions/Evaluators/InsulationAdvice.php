<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\HeatPump;
use Illuminate\Support\Collection;

class InsulationAdvice extends ShouldEvaluate
{
    public function evaluate($value = null, ?Collection $answers = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;

        // This evaluator checks if a given advice is returned by the insulation score. This tells the user
        // they should improve certain aspects of their home before considering a heat pump, because it might not
        // perform to ideal standards.

        $key = md5(json_encode([null]));

        if (array_key_exists($key, $this->override)) {
            $results = $this->override[$key];
            return [
                'results' => $results,
                'bool' => in_array($value, $results),
                'key' => $key,
            ];
        }

        $calculator = new HeatPump($building, $inputSource, $answers);

        // We don't need the return value, but the method sets the advices
        $calculator->insulationScore();

        $results = $calculator->getAdvices();

        return [
            'results' => $results,
            'bool' => in_array($value, $results),
            'key' => $key,
        ];
    }
}