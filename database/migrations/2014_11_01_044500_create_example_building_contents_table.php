<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExampleBuildingContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('example_building_contents', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('example_building_id')->unsigned();
            $table->foreign('example_building_id')->references('id')->on('example_buildings')->onDelete('restrict');

	        $table->integer('build_year')->nullable()->default(null);
	        $table->text('content')->nullable();

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
        Schema::dropIfExists('example_building_contents');
    }
}
