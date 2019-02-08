<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuildingDetailToUserProgressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = DB::table('users')->get();
        $buildingDetailStep = DB::table('steps')->where('slug', 'building-detail')->first();
        $residentInputSource = DB::table('input_sources')->where('short', 'resident')->first();

        foreach ($users as $user) {
            $building = DB::table('buildings')->where('user_id', $user->id)->first();

            if ($building instanceof stdClass) {
                DB::table('user_progresses')->insert([
                    'building_id' => $building->id,
                    'input_source_id' => $residentInputSource->id,
                    'step_id' => $buildingDetailStep->id,
                ]);
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
        $buildingDetailStep = DB::table('steps')->where('slug', 'building-detail')->first();
        DB::table('user_progresses')->where('step_id', $buildingDetailStep->id)->delete();
    }
}
