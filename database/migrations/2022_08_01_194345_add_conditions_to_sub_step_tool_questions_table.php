<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConditionsToSubStepToolQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Schema::hasColumn(
            'sub_step_tool_questions',
            'tool_question_type_id'
        )) {
            Schema::table(
                'sub_step_tool_questions',
                function (Blueprint $table) {
                    $table->unsignedBigInteger(
                        'tool_question_type_id'
                    )->nullable()->after('tool_question_id');
                    $table->foreign('tool_question_type_id')->references(
                        'id'
                    )->on('tool_question_types')->onDelete('cascade');
                }
            );
        }

        if ( ! Schema::hasColumn('sub_step_tool_questions', 'conditions')) {
            Schema::table(
                'sub_step_tool_questions',
                function (Blueprint $table) {
                    $table->json('conditions')->nullable()->default(null)->after('tool_question_type_id');
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop with regular table
    }
}
