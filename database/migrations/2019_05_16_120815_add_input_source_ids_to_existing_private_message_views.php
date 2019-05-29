<?php

use App\Models\BuildingCoachStatus;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use Illuminate\Database\Migrations\Migration;

class AddInputSourceIdsToExistingPrivateMessageViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // get the private messages where we know we can get the used id from
        $privateMessages = DB::table('private_messages')
                             ->where('from_user_id', '!=', null)
                             ->get();

        foreach ($privateMessages as $privateMessage) {
            $groupParticipants = PrivateMessage::getGroupParticipants($privateMessage->building_id);

            foreach ($groupParticipants as $groupParticipant) {
                if ($groupParticipant instanceof \App\Models\User) {


                    // get the connected coaches for the current building
                    $connectedCoachesForBuilding = BuildingCoachStatus::getConnectedCoachesByBuildingId($privateMessage->building_id);

                    // check if the current group participant id added to the buildingCoachStatus
                    // if so the $inputSourceId will be the coach input source id
                    // if not, the group participant is a resident.
                    if ($connectedCoachesForBuilding->contains('coach_id', $groupParticipant->id)) {
                        $inputSourceId = InputSource::findByShort('coach')->id;
                    } else {
                        $inputSourceId = InputSource::findByShort('resident')->id;
                    }

                    // get the private message view.
                    $privateMessageView = DB::table('private_message_views')
                                            ->where('cooperation_id', null)
                                            ->where('read_at', null)
                                            ->where('private_message_id', $privateMessage->id)
                                            ->where('user_id', $groupParticipant->id)
                                            ->first();

                    if ($privateMessageView instanceof stdClass) {
                        DB::table('private_message_views')
                            ->where('private_message_id', $privateMessage->id)
                            ->where('user_id', $groupParticipant->id)
                            ->update(['input_source_id' => $inputSourceId]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('private_message_views')->update(['input_source_id'=> null]);
    }
}
