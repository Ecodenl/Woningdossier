<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingServiceValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_service_values', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_service_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_service_id')->references('id')->on('building_services') ->onDelete('restrict');

            $table->string('name')->default('');
            $table->string('value')->default('');
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
        Schema::dropIfExists('building_service_values');
    }
}
