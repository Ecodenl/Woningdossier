<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Enums\ApiImplementation;

class SupportsLvBag extends ShouldEvaluate
{
    public function evaluate($value = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;
        $answers = $this->answers;

        // This evaluator checks if the given building belongs to a cooperation that supports the LvBag API.
        // $value must be a bool, which indicates if the support is the desired outcome.

        // NOTE: If we want more API support based classes, we could make a reusable extend, or convert value to array

        $key = md5(json_encode([null]));

        if (array_key_exists($key, $this->override)) {
            $bool = $this->override[$key];
            return [
                'results' => $bool,
                'bool' => $bool === $value,
                'key' => $key,
            ];
        }

        $supportsApi = $building->user->cooperation->getCountry()->supportsApi(ApiImplementation::LV_BAG);
\Log::debug("SUPPORTS API?", [$supportsApi]);
        return [
            'results' => $supportsApi,
            'bool' => $supportsApi === $value,
            'key' => $key,
        ];
    }
}