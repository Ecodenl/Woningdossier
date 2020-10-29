<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropVariousColumnsFromBuildingCoachStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_coach_statuses', function (Blueprint $table) {
            $table->dropForeign(['private_message_id']);
            $table->dropColumn('private_message_id');
            $table->dropColumn('appointment_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
