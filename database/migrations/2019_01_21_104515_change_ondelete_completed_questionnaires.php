<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
     */
    public function down(): void
    {
        Schema::table('completed_questionnaires', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['questionnaire_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('restrict');
        });
    }
};
