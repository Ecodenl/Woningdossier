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
        // Note that if you need to do other stuff here, put it ABOVE this method
        // as a $building->syncOriginal() happens in this if statement which will
        // wipe the dirty values.

        /*if ($building->isDirty('example_building_id')) {
            // if example_building_id is dirty, change is true.
            Log::debug(__METHOD__." example_building_id was changed (from " . $building->getOriginal('example_building_id') . " to " . $building->example_building_id);
            $exampleBuilding = ExampleBuilding::find(
                $building->example_building_id
            );
            // Sync the original. We now know that the example building was dirty
            // that's all that was needed.
            // The ExampleBuildingService will be updating BuildingFeatures, so
            // to prevent an infinite loop
            // (BuildingFeaturesObserver -> BuildingObserver -> BuildingFeaturesObserver etc.)
            // we sync the original here.
            $building->syncOriginal();

            if ($exampleBuilding instanceof ExampleBuilding) {
                // Note: dependent on the session for scoping input source id
                // No other way as building does not contain an input source id
                $buildingFeature = $building->buildingFeatures;

                ExampleBuildingService::apply(
                    $exampleBuilding,
                    $buildingFeature->build_year,
                    $building
                );
                // if it's the first time, also fill the master input source.
                if ($this->isFirstTimeToolIsFilled($building)) {
                    ExampleBuildingService::apply(
                        $exampleBuilding,
                        $buildingFeature->build_year,
                        $buildingFeature->building,
                        InputSource::findByShort(InputSource::MASTER_SHORT)
                    );
                }
            } else {
                Log::debug(
                    __METHOD__." No example building was found. Clearing the example building for building ".$building->id
                );
                ExampleBuildingService::clearExampleBuilding($building);
            }
        }*/

        \App\Helpers\Cache\Building::wipe($building->id);
    }

    /**
     * Deleting event.
     */
    public function deleting(Building $building)
    {
        $building->user_id             = null;
        $building->country_code        = 'nl';
        $building->example_building_id = null;
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

//    private function isFirstTimeToolIsFilled(Building $building)
//    {
//        $inputSource      = InputSource::findByShort(InputSource::MASTER_SHORT);
//        $cookTypeQuestion = ToolQuestion::findByShort('cook-type');
//
//        return is_null($building->getAnswer($inputSource, $cookTypeQuestion));
//    }
}
