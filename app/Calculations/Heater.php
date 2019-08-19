<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\Heater\KeyFigures;
use App\Helpers\NumberFormatter;
use App\Models\Building;
use App\Models\ComfortLevelTapWater;
use App\Models\HeaterComponentCost;
use App\Models\Interest;
use App\Models\KeyFigureConsumptionTapWater;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Models\User;
use App\Models\UserEnergyHabit;
use Carbon\Carbon;

class Heater {

    public static function calculate(Building $building, User $user, $calculateData)
    {
        $result = [
            'consumption' => [
                'water' => 0,
                'gas' => 0,
            ],
            'specs' => [
                'size_boiler' => 0,
                'size_collector' => 0,
            ],
            'year' => null,
            'production_heat' => 0,
            'percentage_consumption' => 0,
            'savings_gas' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
            'interest_comparable' => 0,
        ];

        $comfortLevelId = $calculateData['user_energy_habits']['water_comfort_id'] ?? 0;
        $comfortLevel = ComfortLevelTapWater::find($comfortLevelId);
        $interests = $calculateData['interest'] ?? '';

        $habit = $user->energyHabit;

        if ($habit instanceof UserEnergyHabit && $comfortLevel instanceof ComfortLevelTapWater) {
            $consumption = KeyFigures::getCurrentConsumption($habit, $comfortLevel);
            if ($consumption instanceof KeyFigureConsumptionTapWater) {
                $result['consumption'] = [
                    'water' => $consumption->water_consumption,
                    'gas' => $consumption->energy_consumption,
                ];
            }
            \Log::debug('Heater: Current consumption: '.json_encode($result['consumption']));

            $buildingHeaters = $calculateData['building_heaters'] ?? [];
            $angle = $buildingHeaters['angle'] ?? 0;
            $orientationId = $buildingHeaters['pv_panel_orientation_id'] ?? 0;
            $orientation = PvPanelOrientation::find($orientationId);

            $locationFactor = KeyFigures::getLocationFactor($building->postal_code);
            $helpFactor = 1;
            if ($orientation instanceof PvPanelOrientation && $angle > 0) {
                $yield = KeyFigures::getYield($orientation, $angle);
                \Log::debug('Heater: Yield for '.$orientation->name.' at '.$angle.' degrees = '.$yield->yield);
                if ($yield instanceof PvPanelYield && $locationFactor instanceof PvPanelLocationFactor) {
                    \Log::debug('Heater: Location factor for '.$building->postal_code.' is '.$locationFactor->factor);
                    $helpFactor = $yield->yield * $locationFactor->factor;
                }
            }
            \Log::debug('Heater: helpfactor: '.$helpFactor);

            $systemSpecs = KeyFigures::getSystemSpecifications($result['consumption']['water'], $helpFactor);

            if (is_array($systemSpecs) && array_key_exists('boiler', $systemSpecs) && array_key_exists('collector', $systemSpecs)) {
                $result['specs'] = [
                    'size_boiler' => $systemSpecs['boiler'],
                    'size_collector' => $systemSpecs['collector'],
                ];

                \Log::debug('Heater: For this water consumption you need this heater: '.json_encode($systemSpecs));
                $result['production_heat'] = $systemSpecs['production_heat'];
                $result['savings_gas'] = $result['production_heat'] / Kengetallen::gasKwhPerM3();
                $result['percentage_consumption'] = isset($result['consumption']['gas']) ? ($result['savings_gas'] / $result['consumption']['gas']) * 100 : 0;
                $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
                $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));

                $componentCostBoiler = HeaterComponentCost::where('component', 'boiler')->where('size', $result['specs']['size_boiler'])->first();
                $componentCostCollector = HeaterComponentCost::where('component', 'collector')->where('size', $result['specs']['size_collector'])->first();
                $result['cost_indication'] = $componentCostBoiler->cost + $componentCostCollector->cost;

                $result['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);

                foreach ($interests as $type => $interestTypes) {
                    foreach ($interestTypes as $typeId => $interestId) {
                        $interest = Interest::find($interestId);
                    }
                }

                $currentYear = Carbon::now()->year;
                if (1 == $interest->calculate_value) {
                    $result['year'] = $currentYear;
                } elseif (2 == $interest->calculate_value) {
                    $result['year'] = $currentYear + 5;
                }

                if ($helpFactor >= 0.84) {
                    $result['performance'] = [
                        'alert' => 'success',
                        'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.ideal'),
                    ];
                } elseif ($helpFactor < 0.70) {
                    $result['performance'] = [
                        'alert' => 'danger',
                        'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.no-go'),
                    ];
                } else {
                    $result['performance'] = [
                        'alert' => 'warning',
                        'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.possible'),
                    ];
                }
            }
        }
        return $result;
    }
}