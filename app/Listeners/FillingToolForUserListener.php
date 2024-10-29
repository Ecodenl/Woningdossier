<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\BuildingFeature;
use App\Models\InputSource;
use App\Services\Models\BuildingService;

class FillingToolForUserListener
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
        // The building the user wants to fill.
        $building = $event->building;

        // For the tool filling we set the value to our own input source, because we want to see our own values.
        $inputSourceValue = HoomdossierSession::getInputSource(true);

        // When a user registers, they get a building feature with BAG filled data (surface and build year).
        // However, when a coach will check out the building, the data is filled, so it will NOT update.
        // We will fetch the data from master if the coach has no building feature.
        $currentSourceFeature = $building->buildingFeatures()->forInputSource($inputSourceValue)->first();

        if (! $currentSourceFeature instanceof BuildingFeature) {
            $masterFeature =  $building->buildingFeatures()->forInputSource(InputSource::master())->first();
            if ($masterFeature instanceof BuildingFeature) {
                $replica = $masterFeature->replicate();
                $replica->input_source_id = $inputSourceValue->id;
                $replica->save();
            }
        }

        HoomdossierSession::setBuilding($building);
        HoomdossierSession::setInputSourceValue($inputSourceValue);

        $this->buildingService->forBuilding($building)->forInputSource($inputSourceValue)->performMunicipalityCheck();
    }
}
