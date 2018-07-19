<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingUserUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_user_usages', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned()->nullable()->default(null);
            $table->foreign('building_id')->references('id')->on('buildings') ->onDelete('restrict');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users') ->onDelete('restrict');

            $table->integer('usage_percentage')->nullable()->default(null);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

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
        Schema::dropIfExists('building_user_usages');
    }
}
