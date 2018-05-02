<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_values', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('service_id')->unsigned()->nullable()->default(null);
            $table->foreign('service_id')->references('id')->on('services') ->onDelete('restrict');

            $table->uuid('value')->default('');
	        $table->integer('calculate_value')->unsigned()->nullable();
	        $table->integer('order');

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
        Schema::dropIfExists('service_values');
    }
}
