<?php

namespace App\Observers;

use App\Models\Building;

class BuildingObserver
{
    public function saving(Building $building): void
    {
        // Not allowed as null
        $building->extension ??= '';
    }

    public function saved(Building $building): void
    {
        \App\Helpers\Cache\Building::wipe($building->id);
    }

    /**
     * Deleting event.
     */
    public function deleting(Building $building): void
    {
        $building->user_id             = null;
        $building->country_code        = 'nl';
        $building->primary             = 0;
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

    public function deleted(Building $building): void
    {
        \App\Helpers\Cache\Building::wipe($building->id);
    }
}
