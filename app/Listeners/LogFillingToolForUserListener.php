<?php

namespace App\Listeners;

use App\Helpers\Queue;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogFillingToolForUserListener implements ShouldQueue
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
        $userThatIsFillingTool = $event->userThatIsFillingTool;

        Log::create([
            'loggable_type' => User::class,
            'loggable_id' => $userThatIsFillingTool->id,
            'building_id' => $building->id,
            'message' => __('woningdossier.log-messages.filling-tool-for', [
                'full_name' => $userThatIsFillingTool->getFullName(),
                'for_full_name' => $buildingOwner->getFullName(),
                'time' => Carbon::now(),
            ]),
        ]);
    }
}
