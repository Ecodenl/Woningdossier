<?php

namespace App\Listeners;

use App\Events\UserRevokedAccessToHisBuilding;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SetMessagesReadForBuilding
{
    /**
     * Handle the event.
     */
    public function handle(UserRevokedAccessToHisBuilding $event): void
    {
        $building = $event->building;

        // set all the private_messages to read for a specific building
        DB::table('private_messages')
            ->select('private_message_views.*')
            ->where('building_id', $building->id)
            ->leftJoin('private_message_views', 'private_messages.id', 'private_message_views.private_message_id')
            ->update(['read_at' => Carbon::now()]);
    }
}
