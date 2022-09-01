<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeatPumpCharacteristicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'heat_pump_characteristics',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->morphs('heat_pump_configurable', 'hpchar_hpconf');
                $table->unsignedBigInteger('tool_question_custom_value_id')->nullable();
                $table->foreign('tool_question_custom_value_id')->references('id')->on('tool_question_custom_values')->onDelete('cascade');
                $table->decimal('scop', 4, 2);
                $table->decimal('scop_tap_water', 4, 2);
                $table->unsignedInteger('share_percentage_tap_water');
                $table->unsignedInteger('costs');
                $table->unsignedInteger('standard_power_kw');
                $table->string('type'); // 'hybrid' / 'full'
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('heat_pump_characteristics');
    }
}
