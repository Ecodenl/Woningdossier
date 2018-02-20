<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactEnergyHabitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_energy_habits', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('contact_id')->unsigned()->nullable()->default(null);
            $table->foreign('contact_id')->references('id')->on('contacts') ->onDelete('restrict');

            $table->integer('residents_nr')->nullable()->default(null);
            $table->integer('thermostat_high')->nullable()->default(null);
            $table->integer('thermostat_low')->nullable()->default(null);
            $table->integer('hours_high')->nullable()->default(null);
            $table->integer('heating_first_floor')->nullable()->default(null);
            $table->integer('heating_second_floor')->nullable()->default(null);
            $table->integer('heated_space_outside')->nullable()->default(null);
            $table->boolean('cook_gas')->default(false);
            $table->integer('amount_warm_water_id')->nullable()->default(null);
            $table->integer('amount_electricity')->nullable()->default(null);
            $table->integer('amount_gas')->nullable()->default(null);
            $table->integer('amount_water')->nullable()->default(null);
            $table->boolean('motivation_comfort')->default(false);
            $table->boolean('motivation_enviroment')->default(false);
            $table->boolean('motivation_costs')->default(false);
            $table->boolean('motivation_investment')->default(false);
            $table->longText('motivation_extra');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
        Schema::dropIfExists('contact_energy_habits');
    }
}
