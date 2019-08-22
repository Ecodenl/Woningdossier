<?php

namespace App\Listeners;

use App\Models\Log;
use Carbon\Carbon;

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
        $building = $event->building;
        $buildingOwner = $event->buildingOwner;
        $userThatIsFillingTool = $event->userThatIsFillingTool;

        Log::create([
            'user_id' => $userThatIsFillingTool->id,
            'building_id' => $building->id,
            'message' => __('woningdossier.log-messages.observing-tool-for', [
                'full_name' => $userThatIsFillingTool->getFullName(),
                'for_full_name' => $buildingOwner->getFullName(),
                'time' => Carbon::now(),
            ]),
            'for_user_id' => $buildingOwner->id,
        ]);
    }
}
