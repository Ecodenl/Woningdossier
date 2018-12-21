<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCooperationStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperation_steps', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('cooperation_id')->unsigned();
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('restrict');
            
            $table->integer('step_id')->unsigned();
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('restrict');

            $table->integer('order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cooperation_steps');
    }
}
