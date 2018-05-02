<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHeaterSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
    public function down()
    {
        Schema::dropIfExists('heater_specifications');
    }
}
