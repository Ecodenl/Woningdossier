<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingPaintworkStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_paintwork_statuses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('last_painted_year')->unsigned();

            $table->integer('paintwork_status_id')->unsigned()->nullable();
            $table->foreign('paintwork_status_id')->references('id')->on('paintwork_statuses')->onDelete('restrict');

            $table->integer('wood_rot_status_id')->unsigned()->nullable();
            $table->foreign('wood_rot_status_id')->references('id')->on('wood_rot_statuses')->onDelete('restrict');

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
        Schema::dropIfExists('building_paintwork_statuses');
    }
}
