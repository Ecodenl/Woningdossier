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
        Schema::create('user_energy_habits', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->integer('resident_count')->nullable();
            $table->decimal('thermostat_high')->nullable();
            $table->decimal('thermostat_low')->nullable();
            $table->integer('hours_high')->nullable();
            $table->integer('heating_first_floor')->unsigned()->nullable();
            $table->foreign('heating_first_floor')->references('id')->on('building_heatings')->onDelete('restrict');
            $table->integer('heating_second_floor')->unsigned()->nullable();
            $table->foreign('heating_second_floor')->references('id')->on('building_heatings')->onDelete('restrict');
            $table->integer('heated_space_outside')->nullable();
            $table->boolean('cook_gas')->default(false);
            $table->integer('water_comfort_id')->nullable();
            $table->bigInteger('amount_electricity')->nullable();
            $table->bigInteger('amount_gas')->nullable();
            $table->bigInteger('amount_water')->nullable();
            $table->longText('living_situation_extra')->nullable();
            $table->longText('motivation_extra')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_energy_habits');
    }
};
