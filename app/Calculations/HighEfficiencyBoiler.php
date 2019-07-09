<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\NumberFormatter;
use App\Helpers\Translation;
use App\Models\Building;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\User;
use App\Models\UserEnergyHabit;

class HighEfficiencyBoiler {

    public static function calculate(Building $building, User $user, $calculateData)
    {

        $result = [
            'savings_gas' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
            'interest_comparable' => 0,
        ];

        $services = $calculateData['building_services'] ?? [];

        // (there's only one..)
        foreach ($services as $serviceId => $options) {
            $boilerService = Service::find($serviceId);

            if (array_key_exists('service_value_id', $options)) {
                /** @var ServiceValue $boilerType */
                $boilerType = ServiceValue::where('service_id', $boilerService->id)
                                          ->where('id', $options['service_value_id'])
                                          ->first();


                if ($boilerType instanceof ServiceValue) {

                    $boilerEfficiency = $boilerType->keyFigureBoilerEfficiency;
                    if ($boilerEfficiency->heating > 95) {
                        $result['boiler_advice'] = Translation::translate('boiler.already-efficient');
                    }
                }

                if (array_key_exists('extra', $options)) {
                    $year = is_numeric(NumberFormatter::reverseFormat($options['extra'])) ? NumberFormatter::reverseFormat($options['extra']) : 0;

                    $measure = MeasureApplication::byShort('high-efficiency-boiler-replace');
                    //$measure = MeasureApplication::where('short', '=', 'high-efficiency-boiler-replace')->first();
                    //$measure = MeasureApplication::translated('measure_name', 'Vervangen cv ketel', 'nl')->first(['measure_applications.*']);

                    $amountGas = $calculateData['habit']['gas_usage'] ?? null;

                    if ($user->energyHabit instanceof UserEnergyHabit) {
                        $result['savings_gas'] = HighEfficiencyBoilerCalculator::calculateGasSavings($boilerType, $user->energyHabit, $amountGas) ?? '';
                    }
                    $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
                    $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));
                    //$result['cost_indication'] = Calculator::calculateCostIndication(1, $measure);
                    $result['replace_year'] = HighEfficiencyBoilerCalculator::determineApplicationYear($measure, $year);
                    $result['cost_indication'] = Calculator::calculateMeasureApplicationCosts($measure, 1, $result['replace_year'], false);
                    $result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);
                }
            }
        }

        return $result;
    }
}