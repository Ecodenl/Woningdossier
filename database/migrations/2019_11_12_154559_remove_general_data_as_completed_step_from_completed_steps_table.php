<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveGeneralDataAsCompletedStepFromCompletedStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * migration to delete the general data as completed step, this is done in the general data split branch.
     * new inputs have been added so users have to go through it again.
     *
     * @return void
     */
    public function up()
    {
        $generalDataStep = DB::table('steps')
            ->where('short', 'general-data')
            ->first();

        DB::table('completed_steps')
            ->where('step_id', $generalDataStep->id)
            ->delete();

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
