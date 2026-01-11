<?php

namespace App\Helpers;

use App\Helpers\Models\BuildingSettingHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Scan;

class SmallMeasuresSettingHelper
{
    /**
     * Check of kleine maatregelen zichtbaar zijn voor een building binnen een scan.
     */
    public static function isEnabledForBuilding(Building $building, Scan $scan): bool
    {
        $cooperation = $building->user->cooperation;

        // Haal cooperatie-niveau instelling op
        $cooperationEnabled = static::isEnabledForCooperation($cooperation, $scan);

        // Als cooperatie instelling AAN staat: altijd zichtbaar
        if ($cooperationEnabled) {
            return true;
        }

        // Als cooperatie instelling UIT staat: check building override
        $override = BuildingSettingHelper::getSettingValue(
            $building,
            static::getBuildingSettingShort($scan),
            false
        );

        // Cast naar boolean
        return filter_var($override, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Check of kleine maatregelen enabled zijn op cooperatie-niveau voor een scan.
     */
    public static function isEnabledForCooperation(Cooperation $cooperation, Scan $scan): bool
    {
        // Expert scan heeft geen kleine maatregelen
        if ($scan->isExpertScan()) {
            return false;
        }

        $cooperationScan = $cooperation->scans()
            ->where('scans.id', $scan->id)
            ->first();

        if (! $cooperationScan) {
            return true; // Default: aan
        }

        return (bool) ($cooperationScan->pivot->small_measures_enabled ?? true);
    }

    /**
     * Get de building setting short voor een specifieke scan.
     */
    public static function getBuildingSettingShort(Scan $scan): string
    {
        return 'small_measures_enabled_' . $scan->short;
    }

    /**
     * Check of een building override heeft voor een scan.
     */
    public static function hasOverride(Building $building, Scan $scan): bool
    {
        return BuildingSettingHelper::hasOverride(
            $building,
            static::getBuildingSettingShort($scan)
        );
    }

    /**
     * Set de building override voor een scan.
     */
    public static function setOverride(Building $building, Scan $scan, bool $enabled): void
    {
        BuildingSettingHelper::syncSettings($building, [
            static::getBuildingSettingShort($scan) => $enabled ? '1' : null,
        ]);
    }
}
