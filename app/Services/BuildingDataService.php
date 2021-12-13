<?php

namespace App\Services;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BuildingDataService
{
    public static function clearBuildingFromInputSource(Building $building, InputSource $inputSource)
    {
        Log::debug(__CLASS__." Clearing data from input source '".$inputSource->name."'");

        // Delete all building elements
        $building->buildingElements()->forInputSource($inputSource)->delete();
        $building->buildingFeatures()->forInputSource($inputSource)->delete();
        $building->buildingServices()->forInputSource($inputSource)->delete();
        $building->currentInsulatedGlazing()->forInputSource($inputSource)->delete();

        $roofTypesToDelete = $building->roofTypes()->forInputSource($inputSource)->get();
        foreach ($roofTypesToDelete as $roofTypeToDelete) {
            // Manually delete these so the master input source updates with it
            $roofTypeToDelete->delete();
        }

        $building->currentPaintworkStatus()->forInputSource($inputSource)->delete();
        $building->pvPanels()->forInputSource($inputSource)->delete();
        $building->heater()->forInputSource($inputSource)->delete();
        $building->toolQuestionAnswers()->forInputSource($inputSource)->delete();
        if ($building->user instanceof User) {
            // remove interests
            $building->user->userInterests()->forInputSource($inputSource)->delete();
            // remove energy habits
            $building->user->energyHabit()->forInputSource($inputSource)->delete();
        }

        return true;
    }
}
