<?php

namespace App\Calculations;

use App\Traits\FluentCaller;
use App\Traits\HasDynamicAnswers;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\UserEnergyHabit;
use Illuminate\Support\Collection;

abstract class Calculator
{
    use FluentCaller,
        HasDynamicAnswers;

    public ?UserEnergyHabit $energyHabit = null;

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

        // TODO: We probably don't need this anymore
        $this->setEnergyHabit();
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

    protected function setEnergyHabit(): self
    {
        $this->energyHabit = $this->building->user->energyHabit()->forInputSource($this->inputSource)->first();

        return $this;
    }
}