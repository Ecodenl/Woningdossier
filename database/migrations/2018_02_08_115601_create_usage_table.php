<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usages', function (Blueprint $table) {
            $table->increments('id');

            // todo appliance_services?
	        /*
            $table->integer('appliance_service_id')->unsigned()->nullable()->default(null);
            $table->foreign('appliance_service_id')->references('id')->on('appliance_services') ->onDelete('restrict');
			*/

            $table->date('start_period')->nullable()->default(null);
            $table->date('end_period')->nullable()->default(null);
            $table->integer('usage')->unsigned()->nullable()->default(null);
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
        Schema::dropIfExists('usages');
    }
}
