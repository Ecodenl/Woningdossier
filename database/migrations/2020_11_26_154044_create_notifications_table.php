<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('building_id');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');

            $table->integer('input_source_id')->unsigned()->nullable()->default(1);
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');

            $table->string('type');

            $table->uuid('uuid');
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
        Schema::dropIfExists('notifications');
    }
}
