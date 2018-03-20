<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_services', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_id')->references('id')->on('buildings') ->onDelete('restrict');

            $table->integer('measure_id')->unsigned()->nullable()->default(null);
            $table->foreign('measure_id')->references('id')->on('measures') ->onDelete('restrict');

            $table->integer('service_type_id')->unsigned()->nullable()->default(null);
            $table->foreign('service_type_id')->references('id')->on('service_types') ->onDelete('restrict');

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
        Schema::dropIfExists('building_services');
    }
}
