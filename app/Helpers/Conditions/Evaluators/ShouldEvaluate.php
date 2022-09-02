<?php

namespace App\Helpers\Conditions\Evaluators;

use App\Models\InputSource;
use App\Models\Building;
use Illuminate\Support\Collection;

interface ShouldEvaluate
{
    public static function evaluate(Building $building, InputSource $inputSource, ?Collection $answers = null): bool;
}