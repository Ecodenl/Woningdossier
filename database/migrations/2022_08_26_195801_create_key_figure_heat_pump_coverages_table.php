<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('key_figure_heat_pump_coverages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('betafactor', 4,2);
            $table->unsignedBigInteger('tool_question_custom_value_id');
            $table->foreign('tool_question_custom_value_id', 'kfhp_coverages_tqcv_id')->references('id')->on('tool_question_custom_values')->onDelete('cascade');
            $table->unsignedInteger('percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_figure_heat_pump_coverages');
    }
};
