<?php

namespace App\Observers;

use App\Models\Building;
use App\Models\ExampleBuilding;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\ExampleBuildingService;
use Illuminate\Support\Facades\Log;

class BuildingObserver
{
    public function saved(Building $building)
    {
        \App\Helpers\Cache\Building::wipe($building->id);
    }

    /**
     * Deleting event.
     */
    public function deleting(Building $building)
    {
        $building->user_id             = null;
        $building->country_code        = 'nl';
        $building->primary             = false;
        $building->save();

        // delete the privatemessages from the building
        $building->privateMessages()->delete();

        // delete the services from a building
        $building->buildingServices()->allInputSources()->delete();
        // delete the elements from a building
        $building->buildingElements()->allInputSources()->delete();
        // remove the features from a building
        $building->buildingFeatures()->allInputSources()->delete();
        // remove the roof types from a building
        $building->roofTypes()->allInputSources()->delete();
        // remove the heater from a building
        $building->heater()->allInputSources()->delete();
        // remove the solar panels from a building
        $building->pvPanels()->allInputSources()->delete();
        // remove the insulated glazings from a building
        $building->currentInsulatedGlazing()->allInputSources()->delete();
        // remove the paintwork from a building
        $building->currentPaintworkStatus()->allInputSources()->delete();
    }

    public function deleted(Building $building)
    {
        \App\Helpers\Cache\Building::wipe($building->id);
    }

    private function isFirstTimeToolIsFilled(Building $building)
    {
        $inputSource      = InputSource::findByShort(InputSource::MASTER_SHORT);
        $cookTypeQuestion = ToolQuestion::findByShort('cook-type');

        return is_null($building->getAnswer($inputSource, $cookTypeQuestion));
    }
}
