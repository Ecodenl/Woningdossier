<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\InputSource;
use App\Models\Building;

interface ShouldEvaluate
{
    public static function evaluate(Building $building, InputSource $inputSource): bool;
}