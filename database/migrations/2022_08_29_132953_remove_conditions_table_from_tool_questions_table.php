<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveConditionsTableFromToolQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('tool_questions', 'conditions')) {
            Schema::table('tool_questions', function (Blueprint $table) {
                $table->dropColumn('conditions');
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
        if (! Schema::hasColumn('tool_questions', 'conditions')) {
            Schema::table('tool_questions', function (Blueprint $table) {
                $table->json('conditions')->nullable()->default(null)->after('for_specific_input_source_id');
            });
        }
    }
}
