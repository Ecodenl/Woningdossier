<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\MeasureApplication;
use App\Models\UserActionPlanAdvice;

class AdviceCategory extends ShouldEvaluate
{
    public function evaluate($value = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;
        $answers = $this->answers;

        // Check if the user has the advice, and if so, if it's in the correct category.
        // This requires $value to be an array, where
        // 'measure_application' => the short of the measure application,
        // 'category' => the category the advice for the application should be in.

        // We won't do any safety checks, because if this is broken, the seeded data is incorrect.
        $measureApplicationShort = $value['measure_application'];
        $category = $value['category'];

        $key = md5(json_encode(['measure_application' => $measureApplicationShort]));

        if (array_key_exists($key, $this->override)) {
            $advice = $this->override[$key];
            return [
                'results' => $advice,
                'bool' => $advice instanceof UserActionPlanAdvice && $advice->category === $category,
                'key' => $key,
            ];
        }

        $measureApplication = MeasureApplication::findByShort($measureApplicationShort);

        $advice = $building->user->userActionPlanAdvices()
            ->forInputSource($inputSource)
            ->forAdvisable($measureApplication)
            ->first();

        return [
            'results' => $advice,
            'bool' => $advice instanceof UserActionPlanAdvice && $advice->category === $category,
            'key' => $key,
        ];
    }
}
