<?php

namespace App\Helpers;

use App\Helpers\Calculation\RoomTemperatureCalculator;
use App\Helpers\KeyFigures\WallInsulation\Temperature;
use App\Models\Building;
use App\Models\BuildingType;
use App\Models\BuildingTypeElementMaxSaving;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\PriceIndexing;
use App\Models\UserEnergyHabit;
use App\Services\Kengetallen\KengetallenService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RawCalculator
{
    /**
     * Calculate the gas savings for the given building when applying the
     * given Element.
     */
    public static function calculateGasSavings(
        Building $building,
        InputSource $inputSource,
        ElementValue $element,
        UserEnergyHabit $energyHabit,
        $surface,
        $measureAdvice
    )
    {
        $result = 0;
        $building->getBuildingType($inputSource);

        $roomTempCalculator = new RoomTemperatureCalculator($energyHabit);
        $averageHouseTemperature = $roomTempCalculator->getAverageHouseTemperature();
        self::debug(__METHOD__ . ' Average house temperature = ' . $averageHouseTemperature);
        $kengetalEnergySaving = Temperature::energySavingFigureWallInsulation($measureAdvice, $averageHouseTemperature);
        self::debug(__METHOD__ . ' Kengetal energiebesparing = ' . $kengetalEnergySaving);

        if (isset($element->calculate_value) && $element->calculate_value < 3) {
            $result = min(
                $surface * $kengetalEnergySaving,
                self::maxGasSavings($building, $inputSource, $element->element)
            );
            self::debug(__METHOD__ . ' ' . $result . ' = min(' . $surface . ' * ' . $kengetalEnergySaving . ', ' . self::maxGasSavings($building, $inputSource, $element->element) . ')');
        }

        return $result;
    }

    /**
     * Calculate the CO2 savings in kg / m3 gas based on the key figure.
     *
     * @param float|int $gasSavings
     *
     * @return float|int
     */
    public static function calculateCo2Savings($gasSavings)
    {
        $result = $gasSavings * Kengetallen::CO2_SAVING_GAS;
        self::debug(__METHOD__ . ' CO2 besparing: ' . $result . ' = ' . $gasSavings . ' * ' . Kengetallen::CO2_SAVING_GAS);

        return $result;
    }

    /**
     * Calculate the money savings in euro / m3 gas based on the key figure.
     *
     * @param float|int $gasSavings
     */
    public static function calculateMoneySavings($gasSavings, float $euroSavingsGas): float
    {
        $result = $gasSavings * $euroSavingsGas;
        self::debug(__METHOD__ . " Euro's besparing: " . $result . ' = ' . $gasSavings . ' * ' . $euroSavingsGas);

        return $result;
    }

    public static function calculateCostIndication($surface, MeasureApplication $measureApplication)
    {
        if (null == $surface || '0.0' == $surface) {
            $result = 0;
        } else {
            $result = max($surface * $measureApplication->costs, $measureApplication->minimal_costs);
        }

        self::debug(__METHOD__ . ' Cost indication: ' . $result . ' = max(' . $surface . ' * ' . $measureApplication->costs . ', ' . $measureApplication->minimal_costs . ')');

        return $result;
    }

    /**
     * Return the costs of applying a particular measure in a particular year.
     * This takes yearly cost indexing into account.
     *
     * @param MeasureApplication $measure The measure to apply
     * @param null|int|float $number The amount of measures. (might be m2, pieces, etc.)
     * @param null|int $applicationYear The year the measure was executed
     * @param bool $applyIndexing Whether or not to apply indexing
     */
    public static function calculateMeasureApplicationCosts(
        MeasureApplication $measure,
        null|int|float $number,
        ?int $applicationYear = null,
        bool $applyIndexing = true
    ): float|int
    {
        self::debug(__METHOD__ . ' for measure ' . $measure->getTranslation('measure_name', 'nl'));
        if (! is_numeric($number) || $number <= 0) {
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
        self::debug(__METHOD__ . ' Non indexed costs: ' . $total . ' = max(' . $number . ' * ' . $measure->costs . ', ' . $measure->minimal_costs . ')');
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

        self::debug(__METHOD__ . ' Indexed costs: ' . $totalIndexed . ' = ' . $total . ' * ' . (1 + ($costIndex / 100)) . '^' . $yearFactor);

        return $totalIndexed;
    }

    /**
     * Calculate the maximum gas savings in m3 per year.
     * Based on the building, only a max percentage of gas can be saved for
     * particular Elements.
     *
     *
     * @return float|int
     */
    public static function maxGasSavings(Building $building, InputSource $inputSource, Element $element)
    {
        $result = 0;

        $buildingType = $building->getBuildingType($inputSource);
        if ($buildingType instanceof BuildingType) {
            $usages = HighEfficiencyBoilerCalculator::init($building, $inputSource)->calculateGasUsage();
            $usage = $usages['heating']['bruto'];
            $saving = 0;
            $maxSaving = BuildingTypeElementMaxSaving::where('building_type_id', $buildingType->id)
                ->where('element_id', $element->id)
                ->first();

            if ($maxSaving instanceof BuildingTypeElementMaxSaving) {
                $saving = $maxSaving->max_saving;
            }
            self::debug(__METHOD__ . ' Max saving for building_type ' . $buildingType->id . ' + element ' . $element->id . ' (' . $element->short . ') = ' . $saving . '%');
            $result = $usage * ($saving / 100);
            self::debug(__METHOD__ . ' ' . $result . ' = ' . $usage . ' * ' . ($saving / 100));
        }

        // when someone fills in a way to low non realistic gas usage it will be below 0
        // if so we display 0.
        if (Number::isNegative($result)) {
            $result = 0;
        }

        return $result;
    }

    protected static function debug(string $line): void
    {
        if (config('hoomdossier.services.enable_calculation_logging')) {
            Log::channel('calculations')->debug($line);
        }
    }
}
