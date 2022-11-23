<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeyFigureInsulationFactorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_figure_insulation_factors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('insulation_grade', 4, 2);
            $table->decimal('insulation_factor', 4, 2);
            $table->unsignedInteger('energy_consumption_per_m2');
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
        Schema::dropIfExists('key_figure_insulation_factors');
    }
}
