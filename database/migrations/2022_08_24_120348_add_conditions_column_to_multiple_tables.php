<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConditionsColumnToMultipleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('tool_question_valuables', 'conditions')) {
            Schema::table('tool_question_valuables', function (Blueprint $table) {
                $table->json('conditions')->nullable()->default(null)->after('extra');
            });
        }

        if (! Schema::hasColumn('tool_question_custom_values', 'conditions')) {
            Schema::table('tool_question_custom_values', function (Blueprint $table) {
                $table->json('conditions')->nullable()->default(null)->after('extra');
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
        if (Schema::hasColumn('tool_question_valuables', 'conditions')) {
            Schema::table('tool_question_valuables', function (Blueprint $table) {
                $table->dropColumn('conditions');
            });
        }

        if (Schema::hasColumn('tool_question_custom_values', 'conditions')) {
            Schema::table('tool_question_custom_values', function (Blueprint $table) {
                $table->dropColumn('conditions');
            });
        }
    }
}
