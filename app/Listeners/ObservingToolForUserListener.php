<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Services\Models\BuildingService;
use App\Models\InputSource;

class ObservingToolForUserListener
{
    protected BuildingService $buildingService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
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

        $this->buildingService->forBuilding($building)->forInputSource($inputSource)->performMunicipalityCheck();
    }
}
