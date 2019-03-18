<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBuildingCoachStatusesStatusToNewStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // the old statuses
        $statusActive = 'active';
        $statusAppointment = 'appointment';
        $statusNewAppointment = 'new_appointment';
        $statusDone = 'done';

        $bcsActives = DB::table('building_coach_statuses')->where('status', $statusActive)->get();
        // since we dont use the active status as a measurement to see if a coach has access or not
        // we have to update the active to pending and create a new row for it wit hstatus in progress
        foreach ($bcsActives as $bcsActive) {
            DB::table('building_coach_statuses')->where('id', $bcsActive->id)->update([
                'status' => \App\Models\BuildingCoachStatus::STATUS_PENDING
            ]);
            DB::table('building_coach_statuses')->insert([
                'coach_id' => $bcsActive->coach_id,
                'building_id' => $bcsActive->building_id,
                'status' => \App\Models\BuildingCoachStatus::STATUS_IN_PROGRESS
            ]);
        }

        // update the remaining statuses.
        DB::table('building_coach_statuses')->where('status', $statusAppointment)->update([
            'status' => \App\Models\BuildingCoachStatus::STATUS_IN_PROGRESS
        ]);

        DB::table('building_coach_statuses')->where('status', $statusNewAppointment)->update([
            'status' => \App\Models\BuildingCoachStatus::STATUS_IN_PROGRESS
        ]);

        DB::table('building_coach_statuses')->where('status', $statusDone)->update([
            'status' => \App\Models\BuildingCoachStatus::STATUS_EXECUTED
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * Its almost impossible to reverse all the statuses to the original state.
     * But, the pending can be changed back to the active
     * the in_progress will be changed to appointment
     * and executed will be changed to done
     *
     * @return void
     */
    public function down()
    {
        // the old statuses
        $statusDone = 'done';

        // lets call it best effort >.<
        DB::table('building_coach_statuses')->where('status', 'pending')->update([
            'status' => $statusDone
        ]);

        DB::table('building_coach_statuses')->where('status', 'executed')->update([
            'status' => 'done'
        ]);
    }
}
