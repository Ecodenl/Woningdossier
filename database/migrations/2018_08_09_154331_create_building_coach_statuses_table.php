<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingCoachStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_coach_statuses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('coach_id')->unsigned()->nullable();
            $table->foreign('coach_id')->references('id')->on('users')->onDelete('set null');

            $table->integer('building_id')->unsigned()->nullable();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');

            $table->integer('private_message_id')->unsigned()->nullable();
            $table->foreign('private_message_id')->references('id')->on('private_messages')->onDelete('set null');

            $table->string('status');

            $table->dateTime('appointment_date')->nullable();

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
        Schema::dropIfExists('building_coach_statuses');
    }
}
