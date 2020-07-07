<?php

namespace App\Listeners;

use App\Models\Building;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetMessagesUnreadForRevokedUserOnBuilding
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
        /** @var User $revokedParticipant */
        $revokedParticipant = $event->revokedParticipant;
        /** @var Building $building */
        $building = $event->building;

        // set all the private messages read, for the building the user got revoked on.
        \DB::table('private_messages')
            ->select('private_message_views.*')
            ->where('private_messages.building_id', $building->id)
            ->where('private_message_views.user_id', $revokedParticipant->id)
            ->leftJoin('private_message_views', 'private_messages.id', 'private_message_views.private_message_id')
            ->update(['read_at' => Carbon::now()]);
    }
}
