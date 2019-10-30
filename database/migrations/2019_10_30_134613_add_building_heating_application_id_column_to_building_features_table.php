<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuildingHeatingApplicationIdColumnToBuildingFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_features', function (Blueprint $table) {
            $table->integer('building_heating_application_id')->unsigned()->nullable()->default(null)->after('input_source_id');
            $table->foreign('building_heating_application_id')->references('id')->on('building_heating_applications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_features', function (Blueprint $table) {
            $table->dropForeign(['building_heating_application_id']);
            $table->dropColumn('building_heating_application_id');
        });
    }
}
