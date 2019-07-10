<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusForBuildingsThatDoNotHaveBuildingCoachStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $buildings = DB::table('buildings')->get();

        foreach ($buildings as $building) {

            if ($this->buildingHasNoBuildingStatus($building)) {
                $this->attachActiveBuildingStatus($building);
            }
        }
    }

    private function attachActiveBuildingStatus($building)
    {
        DB::table('building_statuses')->insert([
            'building_id' => $building->id,
            'status_id' => DB::table('statuses')->where('short', 'active')->first()->id
        ]);
    }

    private function buildingHasNoBuildingStatus($building)
    {
        return DB::table('building_statuses')->where('building_id', $building->id)->first() instanceof stdClass;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
