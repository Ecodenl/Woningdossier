<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuildingComplaintsColumnToUserEnergyHabitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_energy_habits', function (Blueprint $table) {
            $table->string('building_complaints')->after('renovation_plans')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_energy_habits', function (Blueprint $table) {
            $table->dropColumn('building_complaints');
        });
    }
}
