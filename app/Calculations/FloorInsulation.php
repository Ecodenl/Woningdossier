<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\FloorInsulationCalculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\KeyFigures\FloorInsulation\Temperature;
use App\Helpers\NumberFormatter;
use App\Models\Building;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\MeasureApplication;
use App\Models\User;
use App\Models\UserEnergyHabit;

class FloorInsulation {

    /**
     * Method to calculate the floor insulation savings and such.
     *
     * @param  Building  $building
     * @param $calculateData
     *
     * @return array $result
     */
    public static function calculate(Building $building, $energyHabit, $calculateData): array
    {

        $result = [
            'savings_gas' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
        ];

        $crawlspace = Element::where('short', 'crawlspace')->first();

        $elements = $calculateData['element'] ?? [];

        $buildingElements = $calculateData['building_elements'] ?? [];
        $buildingFeatures = $calculateData['building_features'] ?? [];


        $surface = array_key_exists('insulation_surface', $buildingFeatures) ? $buildingFeatures['insulation_surface'] : 0;

        if (array_key_exists('crawlspace', $buildingElements)) {
            // Check if crawlspace is accessible. If not: show warning!
            if (in_array($buildingElements['crawlspace'], ['unknown'])) {
                $result['crawlspace'] = 'warning';
            }
        }

        $crawlspaceValue = null;
        if (array_key_exists($crawlspace->id, $buildingElements)) {
            if (array_key_exists('element_value_id', $buildingElements[$crawlspace->id])) {
                $crawlspaceValue = ElementValue::where('element_id', $crawlspace->id)
                                               ->where('id', $buildingElements[$crawlspace->id]['element_value_id'])
                                               ->first();
            }
            if (array_key_exists('extra', $buildingElements[$crawlspace->id])) {
                // Check if crawlspace is accessible. If not: show warning!
                if (in_array($buildingElements[$crawlspace->id]['extra'], ['no', 'unknown'])) {
                    $result['crawlspace_access'] = 'warning';
                }
            }
        } else {
            // first page request
            $crawlspaceValue = $crawlspace->values()->orderBy('order')->first();
        }

        if ($crawlspaceValue instanceof ElementValue && $crawlspaceValue->calculate_value >= 45) {
            $advice = Temperature::FLOOR_INSULATION_FLOOR;
        } elseif ($crawlspaceValue instanceof ElementValue && $crawlspaceValue->calculate_value >= 30) {
            $advice = Temperature::FLOOR_INSULATION_BOTTOM;
        } else {
            $advice = Temperature::FLOOR_INSULATION_RESEARCH;
        }

        $insulationAdvice = MeasureApplication::byShort($advice);
        $result['insulation_advice'] = $insulationAdvice->measure_name;

        $floorInsulation = Element::where('short', 'floor-insulation')->first();
        if (array_key_exists($floorInsulation->id, $elements)) {
            $floorInsulationValue = ElementValue::where('element_id', $floorInsulation->id)->where('id', $elements[$floorInsulation->id])->first();
            if ($floorInsulationValue instanceof ElementValue && $energyHabit instanceof UserEnergyHabit) {
                $result['savings_gas'] = FloorInsulationCalculator::calculateGasSavings($building, $floorInsulationValue, $energyHabit, $surface, $advice);
            }

            $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
            $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));
            $result['cost_indication'] = Calculator::calculateCostIndication($surface, $insulationAdvice);
            $result['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);
        }

        return $result;
    }
}