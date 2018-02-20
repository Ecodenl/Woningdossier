<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeasureCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('measure_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->integer('measure')->unsigned()->nullable()->default(null);
            $table->foreign('measure')->references('id')->on('measures') ->onDelete('restrict');

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
        Schema::dropIfExists('measure_categories');
    }
}
