<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\BuildingHeating;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\InsulatingGlazing;
use App\Models\KeyFigureTemperature;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\UserEnergyHabit;
use App\Models\WoodRotStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InsulatedGlazingCalculator
{
    public static function calculateCosts(MeasureApplication $measureApplication, int $m2, int $windows): int|float
    {
        if ($windows <= 0) {
            return 0;
        }
        if ($m2 <= 0) {
            return 0;
        }
        $m2PerWindow = max(1, $m2 / $windows);

        self::debug(__METHOD__ . ' m2PerWindow (' . $m2PerWindow . ') = ' . $m2 . ' / ' . $windows);
        $calcM2 = $windows * $m2PerWindow;
        self::debug(__METHOD__ . ' ' . $calcM2 . ' = ' . $windows . ' * ' . $m2PerWindow);

        return RawCalculator::calculateMeasureApplicationCosts($measureApplication, $calcM2, null, false);
    }

    /**
     * Returns the RAW calculated savings for the given $m2 of $measureApplication.
     * For insulated glazing, all should be added. Therefore the max cap will
     * be done later (via the calculateNetGasSavings).
     */
    public static function calculateRawGasSavings(
        float $m2,
        MeasureApplication $measureApplication,
        BuildingHeating $heating,
        InsulatingGlazing $glazing = null
    ): float
    {
        $query = KeyFigureTemperature::where('measure_application_id', $measureApplication->id)
                ->where('building_heating_id', $heating->id);
        if ($glazing instanceof InsulatingGlazing) {
            $query->where('insulating_glazing_id', $glazing->id);
        }
        $keyFigureTemperature = $query->first();

        $saving = $m2 * $keyFigureTemperature->key_figure;

        self::debug(__METHOD__ . ' ' . $saving . ' = ' . $m2 . ' * ' . $keyFigureTemperature->key_figure);

        return $saving;
    }

    public static function calculateNetGasSavings($saving, Building $building, InputSource $inputSource)
    {
        $maxGasSavings = 0;
        /** @NeedsReview There is no element for insulated-glazing. Only the "general-data" variants of living-room-windows and sleeping-rooms-windows.. both having the same % */
        /** @var Element $element */
        $element = Element::where('short', '=', 'living-rooms-windows')->first();

        $maxGasSavings = RawCalculator::maxGasSavings($building, $inputSource, $element);
        $result = min($saving, $maxGasSavings);

        self::debug(__METHOD__ . ' ' . $result . ' = min(' . $saving . ', ' . $maxGasSavings . ')');

        return $result;
    }

    public static function calculatePaintworkSurface(
        ElementValue $frame,
        array $woodElements,
        float $windowSurface
    ): float
    {
        $number = $frame->calculate_value * $windowSurface;
        self::debug(__METHOD__ . ' ' . $number . ' = ' . $frame->calculate_value . ' * ' . $windowSurface);
        /** @var ElementValue $woodElement */
        foreach ($woodElements as $woodElement) {
            $number += $woodElement->calculate_value;
            self::debug(__METHOD__ . ' Adding wood element (calculate value) ' . $woodElement->calculate_value . ' to the paintwork surface (-> ' . $number . ')');
        }

        return $number;
    }

    public static function determineApplicationYear(
        MeasureApplication $measureApplication,
        PaintworkStatus $paintworkStatus,
        WoodRotStatus $woodRotStatus,
        $lastPaintedYear
    )
    {
        self::debug(__METHOD__);

        if ($lastPaintedYear + $measureApplication->maintenance_interval <= Carbon::now()->year) {
            self::debug(__METHOD__ . ' Last painted is longer than ' . $measureApplication->maintenance_interval . ' years ago.');
            $year = Carbon::now()->year;
        } else {
            $year = $lastPaintedYear + $measureApplication->maintenance_interval;
        }

        // If the paintworks status requires earlier action, that's the year
        $year = min($year, (Carbon::now()->year + $paintworkStatus->calculate_value - 1));
        // If the woodrot status requires earlier action, that's the year
        if (! is_null($woodRotStatus->calculate_value)) {
            $year = min($year, (Carbon::now()->year + $woodRotStatus->calculate_value - 1));
        }

        return $year;
    }

    protected static function debug(string $line): void
    {
        if (config('hoomdossier.services.enable_calculation_logging')) {
            Log::channel('calculations')->debug($line);
        }
    }
}
