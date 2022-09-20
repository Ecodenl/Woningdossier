<?php

namespace App\Calculations;

use App\Traits\FluentCaller;
use App\Traits\HasDynamicAnswers;

abstract class Calculator
{
    use FluentCaller,
        HasDynamicAnswers;

    // TODO: Make uniform
    //public UserEnergyHabit $energyHabit;

    // TODO: Replace with just answers in the long run
    public array $calculateData = [];

    // TODO: Uniform constructor

    // TODO: For now, no uniform amount of parameters is used, so we can't make it abstract
    // abstract public static function calculate();

    abstract public function performCalculations(): array;
}