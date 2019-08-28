<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\NumberFormatter;
use App\Helpers\RoofInsulationCalculator;
use App\Models\Building;
use App\Models\BuildingHeating;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\UserEnergyHabit;
use Carbon\Carbon;
use App\Helpers\RoofInsulation as RoofInsulationHelper;

class RoofInsulation {

    public static function calculate(Building $building, InputSource $inputSource, $calculateData)
    {
        $user = $building->user;
        $energyHabit = $user->energyHabit()->forInputSource($inputSource)->get();

        \Log::debug(__METHOD__);
        $result = [];

        $roofTypeIds = $calculateData['building_roof_types']['id'] ?? [];
        foreach ($roofTypeIds as $roofTypeId) {
            $roofType = RoofType::findOrFail($roofTypeId);
            if ($roofType instanceof RoofType) {
                $cat = RoofInsulationHelper::getRoofTypeCategory($roofType);
                // add as key to result array
                $result[$cat] = [
                    'type' => RoofInsulationHelper::getRoofTypeSubCategory($roofType),
                ];
            }
        }

        \Log::debug(json_encode($result));

        $roofInsulation = Element::where('short', 'roof-insulation')->first();
        $adviceMap = RoofInsulationHelper::getMeasureApplicationsAdviceMap();
        $totalSurface = 0;

        $roofTypes = $calculateData['building_roof_types'];

        //dump("Roof types: ");
        //dump($roofTypes);

        foreach (array_keys($result) as $cat) {
            $insulationRoofSurfaceFormatted = NumberFormatter::reverseFormat($roofTypes[$cat]['insulation_roof_surface'] ?? 0);
            $insulationRoofSurface = is_numeric($insulationRoofSurfaceFormatted) ? $insulationRoofSurfaceFormatted : 0;

            //dump($totalSurface . " += " . $insulationRoofSurface);
            $totalSurface += $insulationRoofSurface;
        }
        //dump("Total surface: " . $totalSurface);

        foreach (array_keys($result) as $cat) {
            // defaults
            $catData = [
                'savings_gas' => 0,
                'savings_co2' => 0,
                'savings_money' => 0,
                'cost_indication' => 0,
                'interest_comparable' => 0,
                'replace' => [
                    'costs' => 0,
                    'year' => null,
                ],
            ];

            $insulationRoofSurfaceFormatted = NumberFormatter::reverseFormat($roofTypes[$cat]['insulation_roof_surface'] ?? 0);
            $insulationRoofSurface = is_numeric($insulationRoofSurfaceFormatted) ? $insulationRoofSurfaceFormatted : 0;

            $surface =  $insulationRoofSurface ?? 0;
            $heating = null;
            // should take the bitumen field

            // A pitched roof with bitumen could be the case earlier on.
            // Not sure if this can never be the case again in the future, but
            // we account for it in this function. Therefor we have to calculate
            // a little bit differently in the case of a pitched roof covered
            // in bitumen instead of roof tiles
            $isBitumenOnPitchedRoof = 'pitched' == $cat && $result['pitched']['type'] == 'bitumen';
            // It's a bitumen roof is the category is not pitched or none (so currently only: flat)
            $isBitumenRoof = ! in_array($cat, ['none', 'pitched']) || $isBitumenOnPitchedRoof;

            if ($isBitumenRoof) {
                \Log::debug("The roof is a bitumen roof");
                $year = isset($roofTypes[$cat]['extra']['bitumen_replaced_date']) ? (int) $roofTypes[$cat]['extra']['bitumen_replaced_date'] : Carbon::now()->year - 10;
                \Log::debug("Bitumen last replacement was set to " . $year);
            } else {
                $year = Carbon::now()->year;
            }

            // default, changes only for roof tiles effect
            $factor = 1;

            $advice = null;
            $objAdvice = null;

            if (isset($roofTypes[$cat]['building_heating_id'])) {
                $heating = BuildingHeating::find($roofTypes[$cat]['building_heating_id']);
            }
            if (isset($roofTypes[$cat]['measure_application_id'])) {
                $measureAdvices = $adviceMap[$cat];
                foreach ($measureAdvices as $strAdvice => $measureAdvice) {
                    if ($roofTypes[$cat]['measure_application_id'] == $measureAdvice->id) {
                        $advice = $strAdvice;
                        // we do this as we don't want the advice to be in
                        // $result['insulation_advice'] as in other calculating
                        // controllers
                        $objAdvice = $measureAdvice;
                    }
                }
            }

            if (isset($roofTypes[$cat]['element_value_id'])) {
                // Current roof insulation level
                $roofInsulationValue = ElementValue::where('element_id', $roofInsulation->id)->where('id', $roofTypes[$cat]['element_value_id'])->first();

                if ($roofInsulationValue instanceof ElementValue && $heating instanceof BuildingHeating && isset($advice)) {
                    if ($energyHabit instanceof UserEnergyHabit) {
                        $catData['savings_gas'] = RoofInsulationCalculator::calculateGasSavings($building, $roofInsulationValue, $energyHabit, $heating, $surface, $totalSurface, $advice);
                    }
                    $catData['savings_co2'] = Calculator::calculateCo2Savings($catData['savings_gas']);
                    $catData['savings_money'] = round(Calculator::calculateMoneySavings($catData['savings_gas']));
                    $catData['cost_indication'] = Calculator::calculateCostIndication($surface, $objAdvice);
                    $catData['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($catData['cost_indication'], $catData['savings_money']), 1);
                    // The replace year is about the replacement of bitumen..
                    $catData['replace']['year'] = RoofInsulationCalculator::determineApplicationYear($objAdvice, $year, $factor);
                }
            }

            /** @var \App\Models\MeasureApplication $replaceMeasure */
            // If tiles condition is set, use the status to calculate the replace moment
            $tilesCondition = isset($roofTypes[$cat]['extra']['tiles_condition']) ? (int) $roofTypes[$cat]['extra']['tiles_condition'] : null;
            if (! is_null($tilesCondition)) {
                $replaceMeasure = MeasureApplication::where('short', 'replace-tiles')->first();
                // no year here. Default is this year. It is incremented by factor * maintenance years
                $year = Carbon::now()->year;
                \Log::debug("Tiles condition was set, year is set to this year as the tiles condition is used as a factor for replacement");
                $roofTilesStatus = RoofTileStatus::find($tilesCondition);
                if ($roofTilesStatus instanceof RoofTileStatus) {
                    $factor = ($roofTilesStatus->calculate_value / 100);
                }
            }

            if ($isBitumenRoof) {
                // If it is a bitumen roof, $year is already set to the best
                // value.
                $replaceMeasure = MeasureApplication::where('short', 'replace-roof-insulation')->first();
            }

            if (isset($replaceMeasure)) {
                $surface = $roofTypes[$cat]['roof_surface'] ?? 0;
                \Log::debug("Calculating costs for replacement measure..");
                $catData['replace']['year'] = RoofInsulationCalculator::determineApplicationYear($replaceMeasure, $year, $factor);
                $catData['replace']['costs'] = Calculator::calculateMeasureApplicationCosts($replaceMeasure, $surface, $catData['replace']['year'], false);
            }

            $result[$cat] = array_merge($result[$cat], $catData);
        }

        \Log::debug(__METHOD__ . " END");
        return $result;
    }
}