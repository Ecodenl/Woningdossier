<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToolQuestionCustomValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tool_question_custom_values', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('tool_question_id');
            $table->foreign('tool_question_id')->references('id')->on('tool_questions')->onDelete('cascade');

            $table->string('short');

            $table->json('name');
            $table->boolean('show');
            $table->integer('order');
            $table->json('extra')->nullable();
            $table->json('conditions')->nullable()->default(null);

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
        Schema::dropIfExists('tool_question_custom_values');
    }
}
