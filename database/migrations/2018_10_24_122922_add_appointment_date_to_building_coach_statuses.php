<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAppointmentDateToBuildingCoachStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_coach_statuses', function (Blueprint $table) {
            $table->dateTime('appointment_date')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // just in kees
        Schema::table('building_coach_statuses', function (Blueprint $table) {
            $table->dropColumn('appointment_date');
        });
    }
}
