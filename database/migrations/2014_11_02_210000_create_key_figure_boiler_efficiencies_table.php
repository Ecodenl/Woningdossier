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
     */
    public function down(): void
    {
        Schema::dropIfExists('key_figure_boiler_efficiencies');
    }
};
