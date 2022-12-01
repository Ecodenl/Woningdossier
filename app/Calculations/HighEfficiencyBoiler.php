<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\UserEnergyHabit;

class HighEfficiencyBoiler extends \App\Calculations\Calculator
{
    public function performCalculations(): array
    {
        $result = [
            'amount_gas' => 0,
            'amount_electricity' => 0,
            'savings_gas' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
            'interest_comparable' => 0,
        ];

        /** @var ServiceValue $boilerType */
        $boilerType = ServiceValue::find($this->getAnswer('boiler-type'));

        if ($boilerType instanceof ServiceValue) {
            $boilerEfficiency = $boilerType->keyFigureBoilerEfficiency;
            if ($boilerEfficiency->heating > 95) {
                $result['boiler_advice'] = __('boiler.already-efficient');
            }
        }

        $measure = MeasureApplication::findByShort('high-efficiency-boiler-replace');

        if ($this->energyHabit instanceof UserEnergyHabit) {
            $result['savings_gas'] = HighEfficiencyBoilerCalculator::calculateGasSavings($boilerType,
                $this->energyHabit);
            $result['amount_gas'] = $this->energyHabit->amount_gas;
            $result['amount_electricity'] = $this->energyHabit->amount_electricity;
        }

        $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
        $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));

        $year = $this->getAnswer('boiler-placed-date');
        $result['replace_year'] = HighEfficiencyBoilerCalculator::determineApplicationYear($measure, $year);
        $result['cost_indication'] = Calculator::calculateMeasureApplicationCosts($measure, 1, $result['replace_year'],
            false);
        $result['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['cost_indication'],
            $result['savings_money']), 1, '.', '');

        return $result;
    }
}
