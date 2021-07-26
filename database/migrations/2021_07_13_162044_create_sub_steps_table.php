<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_steps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name');
            $table->integer('order');
            $table->json('conditions')->nullable()->default(null);
            $table->unsignedInteger('step_id');
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('cascade');

            $table->unsignedBigInteger('sub_step_template_id');
            $table->foreign('sub_step_template_id')->references('id')->on('sub_step_templates')->onDelete('cascade');

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
        Schema::dropIfExists('sub_steps');
    }
}
