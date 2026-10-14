<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Helpers\Hoomdossier;

class SmartTwinEnabled extends ShouldEvaluate
{
    public function evaluate($value = null): array
    {
        // This evaluator checks whether the cooperation of this building runs with SmartTwin driving
        // the technical part of the scan. $value must be a bool, which indicates if that is the
        // desired outcome.

        // There is nothing answer specific to look at, only the cooperation, so that is what the key
        // is made of. An evaluator can be pointed at another building between runs, and a constant
        // key would hand that building the result of the previous one's cooperation.
        $cooperation = Hoomdossier::cooperationOf($this->building);
        $key = md5(json_encode([$cooperation?->id]));

        if (array_key_exists($key, $this->override)) {
            $bool = $this->override[$key];

            return [
                'results' => $bool,
                'bool' => $bool === $value,
                'key' => $key,
            ];
        }

        $enabled = Hoomdossier::smartTwinEnabledFor($cooperation);

        return [
            'results' => $enabled,
            'bool' => $enabled === $value,
            'key' => $key,
        ];
    }
}
