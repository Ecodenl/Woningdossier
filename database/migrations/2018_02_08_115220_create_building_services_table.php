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

            $table->integer('address_id')->unsigned()->nullable()->default(null);
            $table->foreign('address_id')->references('id')->on('addresses') ->onDelete('restrict');

            $table->integer('element')->unsigned()->nullable()->default(null);
            $table->foreign('element')->references('id')->on('measures') ->onDelete('restrict');

            $table->integer('status')->unsigned()->nullable()->default(null);
            $table->foreign('status')->references('id')->on('service_types') ->onDelete('restrict');

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
