<?php

namespace App\Helpers;

use App\Helpers\Calculation\RoomTemperatureCalculator;
use App\Helpers\KeyFigures\WallInsulation\Temperature;
use App\Models\Building;
use App\Models\BuildingTypeElementMaxSaving;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\Log;
use App\Models\MeasureApplication;
use App\Models\PriceIndexing;
use App\Models\UserEnergyHabit;
use Carbon\Carbon;

class Calculator
{
    public static function calculateGasSavings(Building $building, InputSource $inputSource, ElementValue $element, UserEnergyHabit $energyHabit, $surface, $measureAdvice)
    {
        $result = 0;
        $building->getBuildingType($inputSource);

        $roomTempCalculator = new RoomTemperatureCalculator($energyHabit);
        $averageHouseTemperature = $roomTempCalculator->getAverageHouseTemperature();
        self::debug(__METHOD__ . ' Average house temperature = '.$averageHouseTemperature);
        $kengetalEnergySaving = Temperature::energySavingFigureWallInsulation($measureAdvice, $averageHouseTemperature);
        self::debug(__METHOD__ . ' Kengetal energiebesparing = '.$kengetalEnergySaving);

        if (isset($element->calculate_value) && $element->calculate_value < 3) {
            $result = min(
                $surface * $kengetalEnergySaving,
                self::maxGasSavings($building, $inputSource, $energyHabit, $element->element)
            );
            self::debug(__METHOD__ . ' ' . $result.' = min('.$surface.' * '.$kengetalEnergySaving.', '.self::maxGasSavings($building, $inputSource, $energyHabit, $element->element).')');
        }

        return $result;
    }

    public static function calculateCo2Savings($gasSavings)
    {
        $result = $gasSavings * Kengetallen::CO2_SAVING_GAS;
        self::debug(__METHOD__ . ' CO2 besparing: '.$result.' = '.$gasSavings.' * '.Kengetallen::CO2_SAVING_GAS);

        return $result;
    }

    public static function calculateMoneySavings($gasSavings)
    {
        $result = $gasSavings * Kengetallen::EURO_SAVINGS_GAS;
        self::debug(__METHOD__ . " Euro's besparing: ".$result.' = '.$gasSavings.' * '.Kengetallen::EURO_SAVINGS_GAS);

        return $result;
    }

    public static function calculateCostIndication($surface, MeasureApplication $measureApplication)
    {
        if (! $measureApplication instanceof MeasureApplication) {
            return 0;
        }

        if (null == $surface || '0.0' == $surface) {
            $result = 0;
        } else {
            $result = max($surface * $measureApplication->costs, $measureApplication->minimal_costs);
        }

        self::debug(__METHOD__ . ' Cost indication: '.$result.' = max('.$surface.' * '.$measureApplication->costs.', '.$measureApplication->minimal_costs.')');

        return $result;
    }

    /**
     * Return the costs of applying a particular measure in a particular year.
     * This takes yearly cost indexing into account.
     *
     * @param MeasureApplication $measure         The measure to apply
     * @param mixed              $number          The amount of measures. (might be m2, pieces, etc.)
     * @param null|int           $applicationYear
     * @param bool               $applyIndexing   Whether or not to apply indexing
     *
     * @return float|int
     */
    public static function calculateMeasureApplicationCosts(MeasureApplication $measure, $number, $applicationYear = null, $applyIndexing = true)
    {
        self::debug(__METHOD__.' for measure '.$measure->measure_name);
        if ($number <= 0) {
            return 0;
        }
        // if $applicationYear is null, we assume this year.
        if (is_null($applicationYear)) {
            $applicationYear = Carbon::now()->year;
        }
        $yearFactor = $applicationYear - Carbon::now()->year;
        if ($yearFactor < 0) {
            $yearFactor = 0;
        }

        $total = max($number * $measure->costs, $measure->minimal_costs);
        self::debug(__METHOD__.' Non indexed costs: '.$total.' = max('.$number.' * '.$measure->costs.', '.$measure->minimal_costs.')');
        // Apply indexing (general indexing which applies for measures)

        if ($applyIndexing) {
            $index = PriceIndexing::where('short', 'common')->first();
            // default = 2%
            $costIndex = 2;
            if ($index instanceof PriceIndexing) {
                $costIndex = $index->percentage;
            }
        } else {
            $costIndex = 0;
        }

        $totalIndexed = $total * pow((1 + ($costIndex / 100)), $yearFactor);

        self::debug(__METHOD__.' Indexed costs: '.$totalIndexed.' = '.$total.' * '.(1 + ($costIndex / 100)).'^'.$yearFactor);

        return $totalIndexed;
    }

    /**
     * @param float|int $costs    Amount indexed on $fromYear
     * @param int       $fromYear Previous year used for indexing
     * @param int       $toYear   New year to index
     * @param null|int|float|PriceIndexing Null will fall back on default price index (from db). Otherwise a PriceIndex object or "just" a percentage (>= 0, <= 100)
     *
     * @return float|int
     */
    public static function reindexCosts($costs, $fromYear, $toYear, $index = null)
    {
        if (is_null($fromYear)) {
            $fromYear = Carbon::now()->year;
        }
        if (is_null($toYear)) {
            $toYear = Carbon::now()->year;
        }
        $yearFactor = $toYear - $fromYear;

        if (is_null($index)) {
            //$index = PriceIndexing::where('short', 'common')->first();
            $index = \App\Helpers\Cache\Calculator::getPriceIndex('common');
        }
        // default = 2%
        $costIndex = 2;
        if ($index instanceof PriceIndexing) {
            $costIndex = $index->percentage;
        } elseif (! is_null($index) && $index >= 0 && $index <= 100) {
            $costIndex = $index;
        }

        $costsIndexed = $costs * pow((1 + ($costIndex / 100)), $yearFactor);
        self::debug(__METHOD__.' Re-indexed costs: '.$costsIndexed.' = '.$costs.' * '.(1 + ($costIndex / 100)).'^'.$yearFactor);

        return $costsIndexed;
    }

    /**
     * Index costs. Basically this is 'reindexing' where the costs were indexed
     * for this year.
     *
     * @param float|int $costs
     * @param int       $toYear
     *
     * @return float|int
     */
    public static function indexCosts($costs, $toYear)
    {
        return self::reindexCosts($costs, null, $toYear);
    }

    // in m3 per year
    public static function maxGasSavings(Building $building, InputSource $inputSource, $energyHabit, Element $element)
    {
        $boiler = $building->getServiceValue('boiler', $inputSource);

        $buildingType = $building->getBuildingType($inputSource);
        $usages = HighEfficiencyBoilerCalculator::calculateGasUsage($boiler, $energyHabit);
        $usage = $usages['heating']['bruto'];
        $saving = 0;
        $maxSaving = BuildingTypeElementMaxSaving::where('building_type_id', $buildingType->id)
                                                 ->where('element_id', $element->id)
                                                 ->first();

        if ($maxSaving instanceof BuildingTypeElementMaxSaving) {
            $saving = $maxSaving->max_saving;
        }
        self::debug(__METHOD__ . ' Max saving for building_type '.$buildingType->id.' + element '.$element->id.' (' . $element->short . ') = '.$saving.'%');
        $result = $usage * ($saving / 100);
        self::debug(__METHOD__ . ' ' . $result.' = '.$usage.' * '.($saving / 100));

        return $result;
    }

    protected static function debug($line)
    {
        \Log::debug($line);
    }
}
