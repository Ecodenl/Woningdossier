<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('heater_specifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('liters')->unsigned();
            $table->integer('savings')->unsigned();
            $table->integer('boiler')->unsigned();
            $table->decimal('collector', 3, 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('heater_specifications');
    }
};
