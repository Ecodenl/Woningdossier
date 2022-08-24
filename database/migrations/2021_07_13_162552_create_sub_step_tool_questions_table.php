<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubStepToolQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_step_tool_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order');

            $table->unsignedBigInteger('sub_step_id');
            $table->foreign('sub_step_id')->references('id')->on('sub_steps')->onDelete('cascade');

            $table->unsignedBigInteger('tool_question_id');
            $table->foreign('tool_question_id')->references('id')->on('tool_questions')->onDelete('cascade');

            $table->unsignedBigInteger('tool_question_type_id');
            $table->foreign('tool_question_type_id')->references('id')->on('tool_question_types')->onDelete('cascade');

            $table->string('size')->nullable()->default(null);

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
        Schema::dropIfExists('sub_step_tool_questions');
    }
}
