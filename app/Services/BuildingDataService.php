<?php

namespace App\Services;

use App\Models\Building;
use App\Models\InputSource;
use App\Scopes\GetValueScope;

class BuildingDataService
{
    public static function clearBuildingFromInputSource(Building $building, InputSource $inputSource)
    {
        \Log::debug(__CLASS__." Clearing data from input source '".$inputSource->name."'");

        // Delete all building elements
        $building->buildingElements()->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)->delete();
        $building->buildingFeatures()->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)->delete();
        $building->buildingServices()->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)->delete();
        $building->currentInsulatedGlazing()->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)->delete();
        $building->roofTypes()->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)->delete();
        $building->currentPaintworkStatus()->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)->delete();
        $building->pvPanels()->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)->delete();
        $building->heater()->withoutGlobalScope(GetValueScope::class)->where('input_source_id', $inputSource->id)->delete();

        return true;
    }
}
