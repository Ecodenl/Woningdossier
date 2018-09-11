<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeyFigureTemperaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_figure_temperatures', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('measure_application_id')->unsigned();
            $table->foreign('measure_application_id')->references('id')->on('measure_applications')->onDelete('restrict');

            $table->integer('insulating_glazing_id')->unsigned()->nullable();
            $table->foreign('insulating_glazing_id')->references('id')->on('insulating_glazings')->onDelete('restrict');

            $table->integer('building_heating_id')->unsigned();
            $table->foreign('building_heating_id')->references('id')->on('building_heatings')->onDelete('restrict');

            $table->decimal('key_figure', 5, 2);

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
        Schema::dropIfExists('key_figure_temperatures');
    }
}
