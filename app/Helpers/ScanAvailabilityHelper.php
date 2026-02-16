<?php

namespace App\Helpers;

use App\Helpers\Models\BuildingSettingHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionAnswer;

class ScanAvailabilityHelper
{
    /**
     * Check of een scan beschikbaar is voor een building.
     * Expert scan volgt coöperatieniveau (buiten scope).
     * null (geen record) = enabled (code-default).
     */
    public static function isAvailableForBuilding(Building $building, Scan $scan): bool
    {
        // Expert scan: altijd coöperatieniveau, buiten scope
        if ($scan->isExpertScan()) {
            return true;
        }

        // Check of de coöperatie deze scan heeft
        $cooperation = $building->user->cooperation;
        if (! $cooperation->scans->contains('id', $scan->id)) {
            return false;
        }

        // Check BuildingSetting; null (geen record) = enabled (code-default)
        $value = BuildingSettingHelper::getSettingValue(
            $building,
            static::getBuildingSettingShort($scan),
            null
        );

        // null = geen override = enabled (default)
        if (is_null($value)) {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Heeft het building een antwoord op de roof-type ToolQuestion (eerste unieke quick-scan vraag)?
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
     * Kan de scan ingeschakeld worden? Retourneert true of vertaalsleutel met reden.
     */
    public static function canEnable(Building $building, Scan $scan): true|string
    {
        // Quick: altijd inschakelen
        if ($scan->isQuickScan()) {
            return true;
        }

        // Lite: alleen als er geen quick-scan data is
        if ($scan->isLiteScan()) {
            if (static::hasQuickScanData($building)) {
                return 'cooperation/admin/buildings.show.scan-availability.disabled-reasons.quick-data-exists';
            }

            return true;
        }

        return true;
    }

    /**
     * Kan de scan uitgeschakeld worden? Retourneert true of vertaalsleutel met reden.
     * Een scan kan alleen uitgeschakeld worden als de andere scan ook aanstaat.
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
     * Sla scan beschikbaarheid op.
     * true/null → scan enabled (verwijder record)
     * false → '0' opslaan
     */
    public static function setAvailability(Building $building, Scan $scan, bool $enabled): void
    {
        BuildingSettingHelper::syncSettings($building, [
            static::getBuildingSettingShort($scan) => $enabled ? null : '0',
        ]);
    }

    /**
     * Get de building setting short voor een specifieke scan.
     */
    public static function getBuildingSettingShort(Scan $scan): string
    {
        return 'scan_enabled_' . $scan->short;
    }
}
