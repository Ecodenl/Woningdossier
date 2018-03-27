<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeasureServiceTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measure_service_type', function (Blueprint $table) {
	        $table->integer('measure_id')->unsigned()->nullable()->default(null);
	        $table->foreign('measure_id')->references('id')->on('measures') ->onDelete('restrict');
	        $table->integer('service_type_id')->unsigned()->nullable()->default(null);
	        $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('measure_service_type');
    }
}
