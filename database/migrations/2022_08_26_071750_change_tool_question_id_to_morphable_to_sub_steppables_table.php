<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeToolQuestionIdToMorphableToSubSteppablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_steppables', function (Blueprint $table) {
            $table->dropForeign('sub_step_tool_questions_tool_question_id_foreign');
            if (Schema::hasColumn('sub_steppables', 'tool_question_id')) {
                $table->renameColumn('tool_question_id', 'sub_steppable_id');
            }
        });
        // these have to be done separate, otherwise it will index before the renaming. Why you ask ? Because fuck youuu thats why.
        Schema::table('sub_steppables', function (Blueprint $table) {
            $table->string("sub_steppable_type")->nullable()->after('sub_steppable_id');
            $table->index(["sub_steppable_type", "sub_steppable_id"]);
        });

        DB::table('sub_steppables')->update(['sub_steppable_type' => \App\Models\ToolQuestion::class]);
    }

    protected function createIndexName($type, array $columns)
    {
        $index = strtolower('sub_steppables'.'_'.implode('_', $columns).'_'.$type);
        return str_replace(['-', '.'], '_', $index);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_steppables', function (Blueprint $table) {
            $name = "sub_steppable";
            $indexName = null;

            // the createindexName comes from laravel core, however they made it protected because they are sooo coool.
            $table->dropIndex($indexName ?: $this->createIndexName('index', ["{$name}_type", "{$name}_id"]));

            $table->dropColumn("{$name}_type");
            $table->renameColumn('sub_steppable_id', 'tool_question_id');

            $table->foreign('tool_question_id')->references('id')->on('tool_questions')->onDelete('cascade');
        });
    }
}
