<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameForeignKeysOnCompletedStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('completed_steps', function (Blueprint $table) {
            $table->dropForeign('user_progresses_step_id_foreign');
            $table->dropForeign('user_progresses_building_id_foreign');
            $table->dropForeign('user_progresses_input_source_id_foreign');

            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('cascade');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');
        });
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
