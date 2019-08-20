<?php

namespace App\Observer;

use App\Models\Building;
use App\Scopes\GetValueScope;

class BuildingObserver
{

    /**
     * Deleting event.
     *
     * @param  Building  $building
     */
    public function deleting(Building $building)
    {
        $building->user_id = null;
        $building->country_code = 'nl';
        $building->example_building_id = null;
        $building->primary = false;
        $building->save();

        // delete the privatemessages from the building
        $building->privateMessages()->delete();

        // delete the services from a building
        $building->buildingServices()->withoutGlobalScope(GetValueScope::class)->delete();
        // delete the elements from a building
        $building->buildingElements()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the features from a building
        $building->buildingFeatures()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the roof types from a building
        $building->roofTypes()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the heater from a building
        $building->heater()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the solar panels from a building
        $building->pvPanels()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the insulated glazings from a building
        $building->currentInsulatedGlazing()->withoutGlobalScope(GetValueScope::class)->delete();
        // remove the paintwork from a building
        $building->currentPaintworkStatus()->withoutGlobalScope(GetValueScope::class)->delete();
    }
}
