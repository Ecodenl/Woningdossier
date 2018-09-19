<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeyFigureConsumptionTapWatersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_figure_consumption_tap_waters', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('comfort_level_tap_water_id')->unsigned();
            $table->foreign('comfort_level_tap_water_id', 'key_figure_clevel_tap_water_id_foreign')->references('id')->on('comfort_level_tap_waters')->onDelete('restrict');

            $table->integer('resident_count')->unsigned();
            $table->integer('water_consumption')->unsigned();
            $table->integer('energy_consumption')->unsigned();

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
        Schema::dropIfExists('key_figure_consumption_tap_waters');
    }
}
