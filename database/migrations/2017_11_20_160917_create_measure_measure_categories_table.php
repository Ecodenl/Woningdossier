<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeasureMeasureCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measure_measure_categories', function (Blueprint $table) {
	        $table->integer('measure_id')->unsigned()->nullable()->default(null);
	        $table->foreign('measure_id')->references('id')->on('measures') ->onDelete('restrict');
	        $table->integer('measure_category_id')->unsigned()->nullable()->default(null);
	        $table->foreign('measure_category_id')->references('id')->on('measure_categories') ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('measure_measure_categories');
    }
}
