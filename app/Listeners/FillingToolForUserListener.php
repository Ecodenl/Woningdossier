<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Services\Models\BuildingService;

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
    public function handle($event, BuildingService $buildingService)
    {
        // the building the user wants to fill
        $building = $event->building;

        // for the tool filling we set the value to our own input source, we want to see our own values
        $inputSourceValue = HoomdossierSession::getInputSource(true);

        HoomdossierSession::setBuilding($building);
        HoomdossierSession::setInputSourceValue($inputSourceValue);

        $buildingService->forBuilding($building)->performMunicipalityCheck();
    }
}
