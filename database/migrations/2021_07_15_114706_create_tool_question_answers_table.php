<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToolQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tool_question_answers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('building_id');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');

            $table->unsignedInteger('input_source_id');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');

            $table->unsignedBigInteger('tool_question_id');
            $table->foreign('tool_question_id')->references('id')->on('tool_questions')->onDelete('cascade');


            $table->unsignedBigInteger('tool_question_custom_value_id')->nullable()->default(null);
            $table->foreign('tool_question_custom_value_id')->references('id')->on('tool_question_custom_values')->onDelete('cascade');

            $table->text('answer')->nullable();

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
        Schema::dropIfExists('tool_question_answers');
    }
}
