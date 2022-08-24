<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddToolQuestionTypeIdColumnToSubStepToolQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('sub_step_tool_questions', 'tool_question_type_id')) {
            Schema::disableForeignKeyConstraints();

            Schema::table('sub_step_tool_questions', function (Blueprint $table) {
                $table->unsignedBigInteger('tool_question_type_id')->after('tool_question_id');
                $table->foreign('tool_question_type_id')->references('id')->on('tool_question_types')->onDelete('cascade');
            });

            $toolQuestions = DB::table('tool_questions')->get();
            foreach ($toolQuestions as $toolQuestion) {
                DB::table('sub_step_tool_questions')
                    ->where('tool_question_id', $toolQuestion->id)
                    ->update([
                        'tool_question_type_id' => $toolQuestion->tool_question_type_id,
                    ]);
            }

            Schema::table('tool_questions', function (Blueprint $table) {
                $table->dropForeign(['tool_question_type_id']);
                $table->dropColumn('tool_question_type_id');
            });

            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('sub_step_tool_questions', 'tool_question_type_id')) {
            Schema::disableForeignKeyConstraints();

            Schema::table('tool_questions', function (Blueprint $table) {
                $table->unsignedBigInteger('tool_question_type_id')->after('placeholder');
                $table->foreign('tool_question_type_id')->references('id')->on('tool_question_types')->onDelete('cascade');
            });

            $subStepToolQuestions = DB::table('sub_step_tool_questions')->get();
            foreach ($subStepToolQuestions as $toolQuestion) {
                DB::table('tool_questions')
                    ->where('id', $toolQuestion->tool_question_id)
                    ->update([
                        'tool_question_type_id' => $toolQuestion->tool_question_type_id,
                    ]);
            }

            Schema::table('sub_step_tool_questions', function (Blueprint $table) {
                $table->dropForeign(['tool_question_type_id']);
                $table->dropColumn('tool_question_type_id');
            });

            Schema::enableForeignKeyConstraints();
        }
    }
}
