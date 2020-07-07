<?php

namespace App\Listeners;

use Carbon\Carbon;

class SetMessagesReadForBuilding
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $building = $event->building;

        // set all the private_messages to read for a specific building
        \DB::table('private_messages')
            ->select('private_message_views.*')
            ->where('building_id', $building->id)
            ->leftJoin('private_message_views', 'private_messages.id', 'private_message_views.private_message_id')
            ->update(['read_at' => Carbon::now()]);
    }
}
