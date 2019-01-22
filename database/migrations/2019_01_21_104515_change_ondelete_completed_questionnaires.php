<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOndeleteCompletedQuestionnaires extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('completed_questionnaires', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['questionnaire_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('completed_questionnaires', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['questionnaire_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('restrict');
        });
    }
}
