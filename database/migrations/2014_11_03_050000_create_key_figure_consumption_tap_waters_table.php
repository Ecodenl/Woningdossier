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
     */
    public function down(): void
    {
        Schema::dropIfExists('key_figure_consumption_tap_waters');
    }
};
