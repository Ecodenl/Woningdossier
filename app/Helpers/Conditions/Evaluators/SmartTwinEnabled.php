<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Helpers\Hoomdossier;

class SmartTwinEnabled extends ShouldEvaluate
{
    public function evaluate($value = null): array
    {
        // This evaluator checks whether this installation runs with SmartTwin driving the technical
        // part of the scan. $value must be a bool, which indicates if that is the desired outcome.

        // Unlike most evaluators there is nothing building or answer specific to look at: the switch
        // is installation wide. The key is constant for that reason, so the result is computed once
        // per evaluation run and reused.
        $key = md5(json_encode([null]));

        if (array_key_exists($key, $this->override)) {
            $bool = $this->override[$key];

            return [
                'results' => $bool,
                'bool' => $bool === $value,
                'key' => $key,
            ];
        }

        $enabled = Hoomdossier::hasEnabledSmartTwinCalls();

        return [
            'results' => $enabled,
            'bool' => $enabled === $value,
            'key' => $key,
        ];
    }
}
