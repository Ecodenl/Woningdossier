<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->nullable()->default(null)->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->integer('building_id')->nullable()->default(null)->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');

            $table->integer('for_user_id')->nullable()->default(null)->unsigned();
            $table->foreign('for_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('message');

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
        Schema::dropIfExists('logs');
    }
}
