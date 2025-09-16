<?php

namespace App\Calculations;

use App\Traits\FluentCaller;
use App\Traits\HasDynamicAnswers;
use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Support\Collection;

abstract class Calculator
{
    use FluentCaller,
        HasDynamicAnswers;

    /**
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     * @param  \Illuminate\Support\Collection|null  $answers
     */
    public function __construct(Building $building, InputSource $inputSource, ?Collection $answers = null)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->answers = $answers instanceof Collection ? $answers : collect();
    }

    /**
     * Short hand syntax to quickly calculate.
     *
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     * @param  \Illuminate\Support\Collection|null  $answers
     *
     * @return array
     */
    public static function calculate(Building $building, InputSource $inputSource, ?Collection $answers = null): array
    {
        $calculator = new static($building, $inputSource, $answers);

        return $calculator->performCalculations();
    }

    abstract public function performCalculations(): array;
}
