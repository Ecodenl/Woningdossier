<?php

namespace App\Listeners;

use App\Events\ParticipantRevokedEvent;
use App\Models\Building;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SetMessagesUnreadForRevokedUserOnBuilding
{
    /**
     * Handle the event.
     */
    public function handle(ParticipantRevokedEvent $event): void
    {
        /** @var User $revokedParticipant */
        $revokedParticipant = $event->revokedParticipant;
        /** @var Building $building */
        $building = $event->building;

        // set all the private messages read, for the building the user got revoked on.
        DB::table('private_messages')
            ->select('private_message_views.*')
            ->where('private_messages.building_id', $building->id)
            ->where('private_message_views.user_id', $revokedParticipant->id)
            ->leftJoin('private_message_views', 'private_messages.id', 'private_message_views.private_message_id')
            ->update(['read_at' => Carbon::now()]);
    }
}
