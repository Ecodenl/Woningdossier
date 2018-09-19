<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeyFigureBoilerEfficienciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_figure_boiler_efficiencies', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('service_value_id')->unsigned();
            $table->foreign('service_value_id')->references('id')->on('service_values')->onDelete('restrict');

            $table->integer('heating')->unsigned();
            $table->integer('wtw')->unsigned();

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
        Schema::dropIfExists('key_figure_boiler_efficiencies');
    }
}
