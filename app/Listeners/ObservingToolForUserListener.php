<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
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

        // so the user isnt able to save anything
        HoomdossierSession::setIsObserving(true);
    }
}
