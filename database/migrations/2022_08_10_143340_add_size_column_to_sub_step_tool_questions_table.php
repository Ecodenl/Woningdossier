<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeColumnToSubStepToolQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('sub_step_tool_questions', 'size')) {
            Schema::table('sub_step_tool_questions', function (Blueprint $table) {
                $table->string('size')->nullable()->default(null)->after('tool_question_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('sub_step_tool_questions', 'size')) {
            Schema::table('sub_step_tool_questions', function (Blueprint $table) {
                $table->dropColumn('size');
            });
        }
    }
}
