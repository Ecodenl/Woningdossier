<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingElementValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_element_values', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_element_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_element_id')->references('id')->on('building_elements') ->onDelete('restrict');

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
        Schema::dropIfExists('building_element_values');
    }
}
