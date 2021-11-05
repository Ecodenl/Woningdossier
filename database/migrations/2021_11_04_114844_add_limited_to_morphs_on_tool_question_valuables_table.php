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
            if (!Schema::hasColumn('tool_question_valuables', 'limiteable_id')) {
                $table->unsignedBigInteger('limiteable_id')->index()->after('order')->nullable();
                $table->string('limiteable_type')->index()->after('limiteable_id')->nullable();
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
            if (Schema::hasColumn('tool_question_valuables', 'limiteable_id')) {
                $table->dropIndex(['limiteable_id']);
                $table->dropIndex(['limiteable_type']);
                $table->dropColumn('limiteable_id');
                $table->dropColumn('limiteable_type');
            }
        });
    }
}
