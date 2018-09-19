<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExampleBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('example_buildings', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('name');

            $table->integer('building_type_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_type_id')->references('id')->on('building_types')->onDelete('restrict');

            $table->integer('cooperation_id')->unsigned()->nullable()->default(null);
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('restrict');

            $table->integer('order')->nullable();
            $table->boolean('is_default')->default(false);
            // more stuff to come here when specified..
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
        Schema::dropIfExists('example_buildings');
    }
}
