<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitedToMorphsOnToolQuestionValuablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tool_question_valuables', function (Blueprint $table) {
            if (!Schema::hasColumn('tool_question_valuables', 'limited_to_id')) {
                $table->unsignedBigInteger('limited_to_id')->index()->after('order');
                $table->string('limited_to_type')->index()->after('limited_to_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *k
     * @return void
     */
    public function down()
    {
        Schema::table('tool_question_valuables', function (Blueprint $table) {
            if (Schema::hasColumn('tool_question_valuables', 'limited_to_id')) {
                $table->dropColumn('limited_to_id');
                $table->dropColumn('limited_to_type');
                $table->dropIndex(['limited_to_id']);
                $table->dropIndex(['limited_to_type']);
            }
        });
    }
}
