<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToolQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tool_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('short')->nullable()->default(null);
            $table->string('save_in')->nullable()->default(null);
            $table->json('name');
            $table->json('help_text');
            $table->json('placeholder')->nullable()->default(null);
            $table->json('conditions')->nullable()->default(null);
            $table->unsignedBigInteger('tool_question_type_id');
            $table->foreign('tool_question_type_id')->references('id')->on('tool_question_types')->onDelete('cascade');
            $table->boolean('coach')->default(true);
            $table->boolean('resident')->default(true);
            $table->json('options')->nullable()->default(null);
            $table->json('validation')->nullable()->default(null);
            $table->string('unit_of_measure')->nullable()->default(null);
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
        Schema::dropIfExists('tool_questions');
    }
}
