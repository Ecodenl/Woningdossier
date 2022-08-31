<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToolQuestionTypeIdColumnToSubSteppablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('sub_steppables', 'tool_question_type_id')) {
            Schema::disableForeignKeyConstraints();

            // So this migration is technically _before_ the morphs, however as it was added later code wise,
            // it might conflict with a current database state
            $column = Schema::hasColumn('sub_steppables', 'sub_steppable_type') ? 'sub_steppable_type' : 'tool_question_id';

            Schema::table('sub_steppables', function (Blueprint $table) use ($column) {
                $table->unsignedBigInteger('tool_question_type_id')->nullable()->after($column);
                $table->foreign('tool_question_type_id')->references('id')->on('tool_question_types')->onDelete('set null');
            });

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
        if (Schema::hasColumn('sub_steppables', 'tool_question_type_id')) {
            Schema::disableForeignKeyConstraints();

            Schema::table('tool_questions', function (Blueprint $table) {
                $table->unsignedBigInteger('tool_question_type_id')->after('placeholder');
                $table->foreign('tool_question_type_id')->references('id')->on('tool_question_types')->onDelete('cascade');
            });

            Schema::table('sub_steppables', function (Blueprint $table) {
                $table->dropForeign(['tool_question_type_id']);
                $table->dropColumn('tool_question_type_id');
            });

            Schema::enableForeignKeyConstraints();
        }
    }
}
