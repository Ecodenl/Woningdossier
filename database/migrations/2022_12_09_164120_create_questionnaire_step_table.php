<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_step', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('questionnaire_id');
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('cascade');

            $table->unsignedInteger('step_id');
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('cascade');

            $table->unsignedInteger('order')->default(0);

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
        Schema::dropIfExists('questionnaire_step');
    }
}
