<?php

namespace App\Helpers;

use App\Helpers\Models\BuildingSettingHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionAnswer;
use App\Services\CooperationScanService;

class ScanAvailabilityHelper
{
    /**
     * Check if a scan is available for a building.
     * Expert scan follows cooperation level (out of scope).
     * null (no record) = follow cooperation default.
     */
    public static function isAvailableForBuilding(Building $building, Scan $scan): bool
    {
        // Expert scan: always cooperation level, out of scope
        if ($scan->isExpertScan()) {
            return true;
        }

        $value = BuildingSettingHelper::getSettingValue(
            $building,
            static::getBuildingSettingShort($scan),
            null
        );

        // Explicit building override takes precedence
        if (! is_null($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        // No override: follow cooperation default
        return $building->user->cooperation->scans->contains('id', $scan->id);
    }

    /**
     * Check if the building has an answer to the roof-type ToolQuestion (first unique quick-scan question).
     */
    public static function hasQuickScanData(Building $building): bool
    {
        $toolQuestion = ToolQuestion::findByShort('roof-type');

        if (! $toolQuestion) {
            return false;
        }

        return ToolQuestionAnswer::forBuilding($building)
            ->where('tool_question_id', $toolQuestion->id)
            ->where('input_source_id', InputSource::master()->id)
            ->exists();
    }

    /**
     * Can the scan be enabled? Returns true or a translation key with reason.
     */
    public static function canEnable(Building $building, Scan $scan): true|string
    {
        // Quick: can always be enabled
        if ($scan->isQuickScan()) {
            return true;
        }

        // Lite: only if there is no quick-scan data
        if ($scan->isLiteScan()) {
            if (static::hasQuickScanData($building)) {
                return 'cooperation/admin/buildings.show.scan-availability.disabled-reasons.quick-data-exists';
            }

            return true;
        }

        return true;
    }

    /**
     * Can the scan be disabled? Returns true or a translation key with reason.
     * A scan can only be disabled if the other scan is also enabled.
     */
    public static function canDisable(Building $building, Scan $scan): true|string
    {
        if ($scan->isLiteScan()) {
            $quickScan = Scan::quick();
            if ($quickScan && ! static::isAvailableForBuilding($building, $quickScan)) {
                return 'cooperation/admin/buildings.show.scan-availability.disabled-reasons.only-active-scan';
            }

            return true;
        }

        if ($scan->isQuickScan()) {
            $liteScan = Scan::lite();
            if ($liteScan && ! static::isAvailableForBuilding($building, $liteScan)) {
                return 'cooperation/admin/buildings.show.scan-availability.disabled-reasons.only-active-scan';
            }

            return true;
        }

        return true;
    }

    /**
     * Save scan availability. Always stores '1' or '0' explicitly.
     */
    public static function setAvailability(Building $building, Scan $scan, bool $enabled): void
    {
        BuildingSettingHelper::syncSettings($building, [
            static::getBuildingSettingShort($scan) => $enabled ? '1' : '0',
        ]);
    }

    /**
     * Determine the current scan type for a building (quick-scan, lite-scan, or both-scans).
     */
    public static function getCurrentTypeForBuilding(Building $building): string
    {
        $liteScan = Scan::lite();
        $quickScan = Scan::quick();

        $liteAvailable = $liteScan && static::isAvailableForBuilding($building, $liteScan);
        $quickAvailable = $quickScan && static::isAvailableForBuilding($building, $quickScan);

        if ($liteAvailable && $quickAvailable) {
            return 'both-scans';
        }

        if ($liteAvailable) {
            return Scan::LITE;
        }

        return Scan::QUICK;
    }

    /**
     * Sync scan availability for a building based on a type selection (quick-scan, lite-scan, or both-scans).
     */
    public static function syncAvailability(Building $building, string $type): void
    {
        $enabledScans = [
            Scan::QUICK => [Scan::QUICK],
            Scan::LITE => [Scan::LITE],
            'both-scans' => [Scan::QUICK, Scan::LITE],
        ];

        $scansToEnable = $enabledScans[$type] ?? [Scan::QUICK];
        $cooperationScanShorts = $building->user->cooperation->scans->pluck('short')->toArray();

        foreach (Scan::simpleScans()->get() as $scan) {
            $shouldBeEnabled = in_array($scan->short, $scansToEnable);
            $isCooperationDefault = in_array($scan->short, $cooperationScanShorts);

            if ($shouldBeEnabled !== $isCooperationDefault) {
                // Store an override when it differs from the cooperation default
                static::setAvailability($building, $scan, $shouldBeEnabled);
            } else {
                // Clear any existing override when it matches the cooperation default
                static::clearAvailability($building, $scan);
            }
        }
    }

    /**
     * Clear scan availability override (revert to cooperation default).
     */
    public static function clearAvailability(Building $building, Scan $scan): void
    {
        BuildingSettingHelper::syncSettings($building, [
            static::getBuildingSettingShort($scan) => null,
        ]);
    }

    /**
     * Get the building setting short for a specific scan.
     */
    public static function getBuildingSettingShort(Scan $scan): string
    {
        return 'scan_enabled_' . $scan->short;
    }
}
