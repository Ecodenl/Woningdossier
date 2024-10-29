<?php

use Database\Seeders\StatusesTableSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @note its not possible to copy the active / inactive status from the building itself.
     */
    public function up(): void
    {
        $buildingCoachStatuses = DB::table('building_coach_statuses')->get();

        // if there are building coach statuses then we need the statuses data
        if ($buildingCoachStatuses->count() > 0) {
            Artisan::call('db:seed', ['--class' => 'StatusesTableSeeder']);
        }

        $buildings = DB::table('buildings')->get();

        foreach ($buildings as $building) {
            $buildingCoachStatuses = DB::table('building_coach_statuses')->where('building_id', $building->id)->get();

            $this->setBuildingStatus($building, 'active');

            $this->copyBuildingCoachStatuses($buildingCoachStatuses);

            if ('in_active' == $building->status) {
                dump('Setting inactive status for building '.$building->id);
                $this->setBuildingStatus($building, 'inactive');
            }
        }

        // change the pending status to added, better for future reference.
        DB::table('building_coach_statuses')
          ->where('status', 'pending')
          ->update([
              'status' => 'added',
          ]);
    }

    private function copyBuildingCoachStatuses($buildingCoachStatuses)
    {
        // copy the bcs to the building_statuses table
        foreach ($buildingCoachStatuses as $buildingCoachStatus) {
            if ($this->statusIsCopyable($buildingCoachStatus)) {
                dump('Copying status '.$buildingCoachStatus->status.' for building id: '.$buildingCoachStatus->building_id);

                $statusId = $this->getStatusId($buildingCoachStatus);

                // insert a new building status row
                DB::table('building_statuses')->insert([
                    'building_id' => $buildingCoachStatus->building_id,
                    'status_id' => $statusId,
                    'appointment_date' => $buildingCoachStatus->appointment_date,
                    'created_at' => $buildingCoachStatus->created_at,
                ]);

                if ($this->statusIsDeletable($buildingCoachStatus)) {
                    // and delete it, since its copied to the new table..
                    $this->deleteBuildingCoachStatus($buildingCoachStatus);
                }
            }
        }
    }

    private function statusIsDeletable($buildingCoachStatus)
    {
        return ! in_array($buildingCoachStatus->status, ['pending', 'removed']);
    }

    private function setBuildingStatus($building, $status)
    {
        DB::table('building_statuses')->insert([
            'building_id' => $building->id,
            'status_id' => DB::table('statuses')->where('short', $status)->first()->id,
            'created_at' => $building->created_at,
        ]);
    }

    private function deleteBuildingCoachStatus($buildingCoachStatus)
    {
        DB::table('building_coach_statuses')->where('id', $buildingCoachStatus->id)->delete();
    }

    /**
     * @param $buildingCoachStatus
     */
    private function statusIsCopyable($buildingCoachStatus): bool
    {
        return ! in_array($buildingCoachStatus->status, ['removed']);
    }

    /**
     * @param $buildingCoachStatus
     *
     * @return mixed
     */
    private function getStatusId($buildingCoachStatus)
    {
        return DB::table('statuses')
                 ->where('short', $buildingCoachStatus->status)
                 ->first()->id;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
