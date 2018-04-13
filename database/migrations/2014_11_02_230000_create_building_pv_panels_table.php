<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingPvPanelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_pv_panels', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('peak_power')->unsigned()->nullable();
	        $table->integer('number')->unsigned()->default(0);

            $table->integer('pv_panel_orientation_id')->unsigned();
            $table->foreign('pv_panel_orientation_id')->references('id')->on('pv_panel_orientations')->onDelete('restrict');

	        $table->integer('angle')->unsigned()->nullable();

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
        Schema::dropIfExists('building_pv_panels');
    }
}
