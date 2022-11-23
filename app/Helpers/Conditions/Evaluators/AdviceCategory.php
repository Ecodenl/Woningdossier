<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\UserActionPlanAdvice;
use Illuminate\Support\Collection;

class AdviceCategory implements ShouldEvaluate
{
    public static function evaluate(Building $building, InputSource $inputSource, $value = null, ?Collection $answers = null): bool
    {
        // Check if the user has the advice, and if so, if it's in the correct category.
        // This requires $value to be an array, where
        // 'measure_application' => the short of the measure application,
        // 'category' => the category the advice for the application should be in.

        // We won't do any safety checks, because if this is broken, the seeded data is incorrect.
        $measureApplicationShort = $value['measure_application'];
        $category = $value['category'];

        $measureApplication = MeasureApplication::findByShort($measureApplicationShort);

        $advice = $building->user->actionPlanAdvices()
            ->forInputSource($inputSource)
            ->forAdvisable($measureApplication)
            ->first();

        return $advice instanceof UserActionPlanAdvice && $advice->category === $category;
    }
}