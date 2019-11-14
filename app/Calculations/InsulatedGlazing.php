<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\InsulatedGlazingCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\NumberFormatter;
use App\Models\Building;
use App\Models\BuildingHeating;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\InsulatingGlazing;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\UserEnergyHabit;
use App\Models\WoodRotStatus;

class InsulatedGlazing
{
    /**
     * Return the calculate results for the insulated glazings.
     *
     * @param Building    $building
     * @param InputSource $inputSource
     * @param $energyHabit
     * @param $calculateData
     *
     * @return array
     */
    public static function calculate(Building $building, InputSource $inputSource, $energyHabit, $calculateData): array
    {
        $result = [
            'savings_gas' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
            'measure' => [],
        ];

        $userInterests = $calculateData['user_interests'] ?? [];

        $buildingInsulatedGlazings = $calculateData['building_insulated_glazings'] ?? [];

        foreach ($buildingInsulatedGlazings as $measureApplicationId => $buildingInsulatedGlazingsData) {
            $measureApplication = MeasureApplication::find($measureApplicationId);
            $buildingHeatingId = array_key_exists('building_heating_id', $buildingInsulatedGlazingsData) ? $buildingInsulatedGlazingsData['building_heating_id'] : 0;
            $buildingHeating = BuildingHeating::find($buildingHeatingId);
            $insulatedGlazingId = array_key_exists('insulated_glazing_id', $buildingInsulatedGlazingsData) ? $buildingInsulatedGlazingsData['insulated_glazing_id'] : 0;
            $insulatedGlazing = InsulatingGlazing::find($insulatedGlazingId);
            $interestId = $userInterests[$measureApplicationId]['interest_id'] ?? null;
            $interest = Interest::find($interestId);

            if ($measureApplication instanceof MeasureApplication && $buildingHeating instanceof BuildingHeating && $interest instanceof Interest && $interest->calculate_value <= 3) {
                $gasSavings = InsulatedGlazingCalculator::calculateRawGasSavings(
                    NumberFormatter::reverseFormat($buildingInsulatedGlazingsData['m2']),
                    $measureApplication,
                    $buildingHeating,
                    $insulatedGlazing
                );

                $result['measure'][$measureApplication->id] = [
                    'costs' => InsulatedGlazingCalculator::calculateCosts($measureApplication, $interest, (int) $buildingInsulatedGlazingsData['m2'], (int) $buildingInsulatedGlazingsData['windows']),
                    'savings_gas' => $gasSavings,
                    // calculated outside this foreach
                    //'savings_co2' => Calculator::calculateCo2Savings($gasSavings),
                    //'savings_money' => Calculator::calculateMoneySavings($gasSavings),
                ];

                $result['cost_indication'] += $result['measure'][$measureApplication->id]['costs'];
                $result['savings_gas'] += $gasSavings;
                // calculated outside this foreach
                /*
                $result['savings_co2'] += $result['measure'][$measureApplication->id]['savings_co2'];
                $result['savings_money'] += $result['measure'][$measureApplication->id]['savings_money'];
                */
            }
        }

        // normalize the measures (glazing options). This should be done as follows:
        // result[savings_gas] = gas savings, taking into account the max gas savings
        // after that, for every glazing measure the savings_gas for that measure
        // should be relative to that
        $rawTotalSavingsGas = $result['savings_gas'];

        \Log::debug(__METHOD__.' Raw total gas savings: '.$rawTotalSavingsGas);

        // note: first no instanceof was used
        // now calculate the net gas savings (based on the sum of all measure applications)
        // + calculate and add savings_co2 and savings_money to the result structure
        $result['savings_gas'] = InsulatedGlazingCalculator::calculateNetGasSavings($rawTotalSavingsGas, $building, $inputSource, $energyHabit);

        \Log::debug(__METHOD__.' Net total gas savings: '.$result['savings_gas']);

        $result['savings_co2'] += Calculator::calculateCo2Savings($result['savings_gas']);
        $result['savings_money'] += Calculator::calculateMoneySavings($result['savings_gas']);

        // Normalize (update) and add data to the result per measure
        foreach ($result['measure'] as $maId => $measureCalculationResults) {
            $rawMeasureSavingsGas = $result['measure'][$maId]['savings_gas'];
            // prevent $rawMeasureSavingsGas  / $rawTotalSavings gas to be > 1
            // otherwise we could still end up saving more than the building would allow..
            $measureGasSavings = $rawTotalSavingsGas > 0 ? min(1, ($rawMeasureSavingsGas / $rawTotalSavingsGas)) * $result['savings_gas'] : 0;
            $result['measure'][$maId]['savings_gas'] = $measureGasSavings;
            \Log::debug(__METHOD__.' Measure '.$maId.' factor: '.$measureGasSavings);
            $result['measure'][$maId]['savings_co2'] = Calculator::calculateCo2Savings($result['measure'][$maId]['savings_gas']);
            $result['measure'][$maId]['savings_money'] = Calculator::calculateMoneySavings($result['measure'][$maId]['savings_gas']);
        }

        // normalization done

        $result['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);

        $result['paintwork'] = [
            'costs' => 0,
            'year' => null,
        ];

        $frames = Element::where('short', 'frames')->first();
        $buildingElements = $calculateData['building_elements'] ?? [];
        $framesValueId = 0;
        if (array_key_exists($frames->id, $buildingElements) && array_key_exists('frames', $buildingElements[$frames->id])) {
            $framesValueId = (int) $buildingElements[$frames->id]['frames'];
        }
        $frameElementValue = ElementValue::find($framesValueId);

        // only applies for wooden frames
        if ($frameElementValue instanceof ElementValue && 'frames' == $frameElementValue->element->short/* && $frameElementValue->calculate_value > 0*/) {
            $windowSurface = 0;

            $windowSurfaceFormatted = NumberFormatter::reverseFormat($calculateData['window_surface'] ?? 0);
            if (is_numeric($windowSurfaceFormatted)) {
                $windowSurface = $windowSurfaceFormatted;
            }
            // frame type use used as ratio (e.g. wood + some others -> use 70% of surface)
            $woodElementValues = [];

            foreach ($buildingElements as $short => $serviceIds) {
                if ('wood-elements' == $short) {
                    foreach ($serviceIds as $serviceId => $ids) {
                        foreach (array_keys($ids) as $id) {
                            $woodElementValue = ElementValue::where('id', $id)->where('element_id',
                                $serviceId)->first();

                            if ($woodElementValue instanceof ElementValue && $woodElementValue->element->short == $short) {
                                $woodElementValues[] = $woodElementValue;
                            }
                        }
                    }
                }
            }

            $measureApplication = MeasureApplication::where('short', 'paint-wood-elements')->first();

            $number = InsulatedGlazingCalculator::calculatePaintworkSurface($frameElementValue, $woodElementValues, NumberFormatter::reverseFormat($windowSurface));

            $buildingPaintworkStatuses = $calculateData['building_paintwork_statuses'] ?? [];
            $paintworkStatus = null;
            $woodRotStatus = null;
            $lastPaintedYear = 2000;
            if (array_key_exists('paintwork_status_id', $buildingPaintworkStatuses)) {
                $paintworkStatus = PaintworkStatus::find($buildingPaintworkStatuses['paintwork_status_id']);
            }
            if (array_key_exists('wood_rot_status_id', $buildingPaintworkStatuses)) {
                $woodRotStatus = WoodRotStatus::find($buildingPaintworkStatuses['wood_rot_status_id']);
            }
            if (array_key_exists('last_painted_year', $buildingPaintworkStatuses)) {
                $year = (int) $buildingPaintworkStatuses['last_painted_year'];
                if ($year > 1950) {
                    $lastPaintedYear = $year;
                }
            }

            $year = 0;
            $costs = 0;

            if ($measureApplication instanceof MeasureApplication && $paintworkStatus instanceof PaintworkStatus) {
                $year = InsulatedGlazingCalculator::determineApplicationYear($measureApplication, $paintworkStatus, $woodRotStatus, $lastPaintedYear);
                $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year, false);
            }

            $result['paintwork'] = compact('costs', 'year');
        }

        return $result;
    }
}
