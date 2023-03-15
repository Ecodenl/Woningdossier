<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Jobs\CheckBuildingAddress;
use App\Jobs\RefreshRegulationsForBuildingUser;
use App\Models\InputSource;

class ObservingToolForUserListener
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
        // the building we want to observe
        $building = $event->building;

        // when we observe the tool, we want to see the tool as a resident it would see.
        $inputSourceValue = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        HoomdossierSession::setBuilding($building);
        HoomdossierSession::setInputSource($inputSource);
        HoomdossierSession::setInputSourceValue($inputSourceValue);

        // so the user isn't able to save anything
        HoomdossierSession::setIsObserving(true);

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
