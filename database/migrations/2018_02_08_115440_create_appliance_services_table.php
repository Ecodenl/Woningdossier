<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplianceServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appliance_services', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('appliance_id')->unsigned()->nullable()->default(null);
            $table->foreign('appliance_id')->references('id')->on('appliances') ->onDelete('restrict');

            $table->integer('building_service_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_service_id')->references('id')->on('building_services') ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *s
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appliance_services');
    }
}
