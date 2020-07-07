<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStepIdForCrackSealing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('measure_applications')
          ->where('short', '=', 'crack-sealing')
          ->where('step_id', '=', 4)
            ->update(['step_id' => 2]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('measure_applications')
          ->where('short', '=', 'crack-sealing')
          ->where('step_id', '=', 2)
          ->update(['step_id' => 4]);
    }
}