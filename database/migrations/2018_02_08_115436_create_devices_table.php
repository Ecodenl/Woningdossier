<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('measure_id')->unsigned()->nullable()->default(null);
            $table->foreign('measure_id')->references('id')->on('measures')->onDelete('restrict');

            $table->integer('building_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('device_type_id')->unsigned()->nullable()->default(null);
            $table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('restrict');

            $table->string('name')->default('');
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
        Schema::dropIfExists('devices');
    }
}
