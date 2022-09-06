<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\NumberFormatter;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\UserEnergyHabit;

class HighEfficiencyBoiler
{
    public static function calculate($energyHabit, $calculateData)
    {
        $result = [
            'savings_gas' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
            'interest_comparable' => 0,
        ];

        $buildingServiceData = $calculateData['building_services'] ?? [];

        $boilerService = Service::where('short', 'boiler')->first();

        if (array_key_exists('service_value_id', $buildingServiceData)) {
            /** @var ServiceValue $boilerType */
            $boilerType = ServiceValue::where('service_id', $boilerService->id)
                ->where('id', $buildingServiceData['service_value_id'])
                ->first();

            if ($boilerType instanceof ServiceValue) {
                $boilerEfficiency = $boilerType->keyFigureBoilerEfficiency;
                if ($boilerEfficiency->heating > 95) {
                    $result['boiler_advice'] = __('boiler.already-efficient');
                }
            }

            if (array_key_exists('extra', $buildingServiceData)) {
                $date = NumberFormatter::reverseFormat($buildingServiceData['extra']['date']);
                $year = is_numeric($date) ? NumberFormatter::reverseFormat($date) : 0;

                $measure = MeasureApplication::byShort('high-efficiency-boiler-replace');

                $amountGas = $calculateData['user_energy_habits']['amount_gas'] ?? null;

                if ($energyHabit instanceof UserEnergyHabit) {
                    $result['savings_gas'] = HighEfficiencyBoilerCalculator::calculateGasSavings($boilerType, $energyHabit, $amountGas);
                    $result['amount_gas'] = $amountGas ?? $energyHabit->amount_gas;
                    $result['amount_electricity'] = $energyHabit->amount_electricity;
                }
                $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
                $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));
                $result['replace_year'] = HighEfficiencyBoilerCalculator::determineApplicationYear($measure, $year);
                $result['cost_indication'] = Calculator::calculateMeasureApplicationCosts($measure, 1, $result['replace_year'], false);
                $result['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);
            }
        }

        return $result;
    }
}
