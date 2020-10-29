<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalInstalledPowerColumnToBuildingPvPanelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_pv_panels', function (Blueprint $table) {
            $table->integer('total_installed_power')->after('input_source_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_pv_panels', function (Blueprint $table) {
            $table->dropColumn('total_installed_power');
        });
    }
}
