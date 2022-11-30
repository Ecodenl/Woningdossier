<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Calculations\Heater;
use Illuminate\Support\Collection;

class SunBoilerPerformance extends ShouldEvaluate
{
    public function evaluate($value = null, ?Collection $answers = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;

        // This evaluator checks the performance for the sun-boiler in the user's situation. The calculation
        // returns a given color which defines the performance.

        $key = md5(json_encode([null]));

        if (array_key_exists($key, $this->override)) {
            $results = $this->override[$key];
            return [
                'results' => $results,
                'bool' => data_get($results, 'performance.alert') === $value,
                'key' => $key,
            ];
        }

        $results = Heater::calculate(
            $building,
            $inputSource,
            $answers,
        );

        return [
            'results' => $results,
            'bool' => data_get($results, 'performance.alert') === $value,
            'key' => $key,
        ];
    }
}