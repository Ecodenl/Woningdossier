<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeyFigureHeatPumpCoveragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_figure_heat_pump_coverages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('betafactor', 4,2);
            $table->unsignedBigInteger('tool_question_custom_value_id');
            $table->foreign('tool_question_custom_value_id')->references('id')->on('tool_question_custom_values')->onDelete('cascade');
            $table->unsignedInteger('percentage');
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
        Schema::dropIfExists('key_figure_heat_pump_coverages');
    }
}
