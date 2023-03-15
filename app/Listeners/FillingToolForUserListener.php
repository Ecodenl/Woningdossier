<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Jobs\CheckBuildingAddress;
use App\Jobs\RefreshRegulationsForBuildingUser;

class FillingToolForUserListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        // the building the user wants to fill
        $building = $event->building;

        // for the tool filling we set the value to our own input source, we want to see our own values
        $inputSourceValue = HoomdossierSession::getInputSource(true);

        HoomdossierSession::setBuilding($building);
        HoomdossierSession::setInputSourceValue($inputSourceValue);

        $currentMunicipality = $building->municipality_id;
        CheckBuildingAddress::dispatchSync($building);
        // Get a fresh (and updated) building instance
        $building = $building->fresh();
        $newMunicipality = $building->municipality_id;

        // If the municipality hasn't changed, we will manually dispatch a refresh. Otherwise, it will happen in the
        // CheckBuildingAddress logic train. We won't dispatch if no municipality is present.
        if (! is_null($newMunicipality) && $currentMunicipality === $newMunicipality) {
            RefreshRegulationsForBuildingUser::dispatch($building);
        }
    }
}
