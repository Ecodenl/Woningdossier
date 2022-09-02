<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConditionsColumnToSubSteppablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('sub_steppables', 'conditions')) {
            Schema::table('sub_steppables', function (Blueprint $table) {
                $table->json('conditions')->after('tool_question_type_id')->nullable()->default(null);
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
        if (Schema::hasColumn('sub_steppables', 'conditions')) {
            Schema::table('sub_steppables', function (Blueprint $table) {
                $table->dropColumn('conditions');
            });
        }
    }
}
