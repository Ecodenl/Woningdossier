<?php

namespace App\Helpers;

use App\Helpers\Models\BuildingSettingHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Scan;

class SmallMeasuresSettingHelper
{
    /**
     * Check if small measures are enabled for a building within a scan.
     */
    public static function isEnabledForBuilding(Building $building, Scan $scan): bool
    {
        // Lite scan always requires small measures, cannot be overridden
        if ($scan->isLiteScan()) {
            return true;
        }

        $override = BuildingSettingHelper::getSettingValue(
            $building,
            static::getBuildingSettingShort($scan),
            null
        );

        // Explicit building override takes precedence
        if (! is_null($override)) {
            return filter_var($override, FILTER_VALIDATE_BOOLEAN);
        }

        // No override: follow cooperation default
        return static::isEnabledForCooperation($building->user->cooperation, $scan);
    }

    /**
     * Check if small measures are enabled at cooperation level for a scan.
     */
    public static function isEnabledForCooperation(Cooperation $cooperation, Scan $scan): bool
    {
        // Expert scan has no small measures
        if ($scan->isExpertScan()) {
            return false;
        }

        // Lite scan always requires small measures
        if ($scan->isLiteScan()) {
            return true;
        }

        $cooperationScan = $cooperation->scans()
            ->where('scans.id', $scan->id)
            ->first();

        if (! $cooperationScan) {
            return true; // Default: enabled
        }

        return (bool) ($cooperationScan->pivot->small_measures_enabled ?? true);
    }

    /**
     * Get the building setting short for a specific scan.
     */
    public static function getBuildingSettingShort(Scan $scan): string
    {
        return 'small_measures_enabled_' . $scan->short;
    }

    /**
     * Check if a building has an override for a scan.
     */
    public static function hasOverride(Building $building, Scan $scan): bool
    {
        return BuildingSettingHelper::hasOverride(
            $building,
            static::getBuildingSettingShort($scan)
        );
    }

    /**
     * Set the building override for a scan.
     */
    public static function setOverride(Building $building, Scan $scan, bool $enabled): void
    {
        BuildingSettingHelper::syncSettings($building, [
            static::getBuildingSettingShort($scan) => $enabled ? '1' : '0',
        ]);
    }

    /**
     * Remove the building override for a scan (revert to cooperation default).
     */
    public static function clearOverride(Building $building, Scan $scan): void
    {
        BuildingSettingHelper::syncSettings($building, [
            static::getBuildingSettingShort($scan) => null,
        ]);
    }
}
