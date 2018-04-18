<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingInsulatedGlazingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_insulated_glazings', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('measure_application_id')->unsigned();
            $table->foreign('measure_application_id')->references('id')->on('measure_applications')->onDelete('restrict');

            $table->integer('insulating_glazing_id')->unsigned()->nullable();
            $table->foreign('insulating_glazing_id')->references('id')->on('insulating_glazings')->onDelete('restrict');

            $table->integer('building_heating_id')->unsigned()->nullable();
            $table->foreign('building_heating_id')->references('id')->on('building_heatings')->onDelete('restrict');

            $table->integer('m2')->unsigned()->nullable()->default(null);
            $table->integer('windows')->unsigned()->nullable()->default(null);
            $table->string('extra')->nullable()->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('building_insulated_glazings');
    }
}
