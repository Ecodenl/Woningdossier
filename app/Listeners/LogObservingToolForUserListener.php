<?php

namespace App\Listeners;

use App\Helpers\Queue;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogObservingToolForUserListener implements ShouldQueue
{
    public $queue = Queue::LOGS;
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
        $buildingOwner = $building->user;
        $userThatIsObservingTool = $event->userThatIsObservingTool;

        Log::create([
            'loggable_type' => User::class,
            'loggable_id' => $userThatIsObservingTool->id,
            'building_id' => $building->id,
            'message' => __('woningdossier.log-messages.observing-tool-for', [
                'full_name' => $userThatIsObservingTool->getFullName(),
                'for_full_name' => $buildingOwner->getFullName(),
                'time' => Carbon::now(),
            ]),
        ]);
    }
}
