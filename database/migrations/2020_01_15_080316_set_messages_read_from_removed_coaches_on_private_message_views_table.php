<?php

use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetMessagesReadFromRemovedCoachesOnPrivateMessageViewsTable extends Migration
{
    use \App\Traits\DebugableMigrationTrait;

    /**
     * Get the building coach statuses where a user is removed from
     */
    public function getRemovedBcsFromUser($user)
    {
        $userId = $user->id;

        $pendingCount = \DB::raw('(
                SELECT coach_id, building_id, count(`status`) AS count_added
	            FROM building_coach_statuses
	            WHERE coach_id = ' . $userId . ' AND `status` = \'' . BuildingCoachStatus::STATUS_ADDED . ' \'
	            group by coach_id, building_id
            )  AS bcs2');
        $removedCount = \DB::raw('(
                SELECT building_id, coach_id, count(`status`) AS count_removed
	            FROM building_coach_statuses
	            WHERE coach_id = ' . $userId . ' AND `status` = \'' . BuildingCoachStatus::STATUS_REMOVED . ' \'
	            group by coach_id, building_id
            ) AS bcs3');


        // query to get the buildings a user is connected to
        return \DB::query()->select('bcs2.coach_id', 'bcs2.building_id', 'bcs2.count_added AS count_added',
            'bcs3.count_removed AS count_removed')
            // count the pending statuses
            ->from($pendingCount)
            // count the removed count
            ->leftJoin($removedCount, 'bcs2.building_id', '=', 'bcs3.building_id')
            // check the building permissions
            // get the buildings
            ->leftJoin('buildings', 'bcs2.building_id', '=', 'buildings.id')
            // check if the building its user / resident is associated with the given cooperation
            ->whereRaw('(count_removed >= count_added)')
            ->where('buildings.deleted_at', '=', null)
            // accept from the cooperation-building-link
            ->groupBy('building_id', 'coach_id', 'count_removed', 'count_added')
            ->get();


    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = User::withoutGlobalScopes()->get();
        foreach ($users as $user) {
            $removedBuildingsFromUser = $this->getRemovedBcsFromUser($user);
            foreach ($removedBuildingsFromUser as $buildingCoachStatus) {
                $privateMessages = PrivateMessage::where('building_id', $buildingCoachStatus->building_id)->get();

                foreach ($privateMessages as $privateMessage) {

                    $unreadMessagesForRemovedCoach = DB::table('private_message_views')
                        ->where('private_message_id', $privateMessage->id)
                        ->where('user_id', $user->id)
                        ->where('input_source_id', InputSource::findByShort('coach')->id)
                        ->where('read_at', null)->count();

                    if ($unreadMessagesForRemovedCoach > 0) {


                        DB::table('private_message_views')->where('private_message_id', $privateMessage->id)
                            ->where('user_id', $user->id)
                            ->where('input_source_id', InputSource::findByShort('coach')->id)
                            ->where('read_at', null)
                            ->update([
                                'read_at' => \Carbon\Carbon::now()
                            ]);
                        $this->line("coach_id: {$user->id}");
                        $this->line("private_message_id: {$privateMessage->id}");
                        $this->line("TOTAL: {$unreadMessagesForRemovedCoach} messages have been set read for removed coach");
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
        // thats quit hard to do.
    }
}
