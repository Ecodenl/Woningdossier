<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveBuildingDetailFromStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $buildingDetailStep = DB::table('steps')->where('slug', 'building-detail')->first();

        if ($buildingDetailStep instanceof stdClass) {
            DB::table('cooperation_steps')
                ->where('step_id', $buildingDetailStep->id)
                ->delete();

            DB::table('steps')->where('id', $buildingDetailStep->id)->delete();

            DB::table('completed_steps')
                ->where('step_id', $buildingDetailStep->id)
                ->delete();
        }
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
