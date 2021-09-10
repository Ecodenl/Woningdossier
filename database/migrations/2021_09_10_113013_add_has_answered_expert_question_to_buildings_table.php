<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasAnsweredExpertQuestionToBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('buildings', 'has_answered_expert_question')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->boolean('has_answered_expert_question')->default(false)->after('user_id');
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
        if (Schema::hasColumn('buildings', 'has_answered_expert_question')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->dropColumn('has_answered_expert_question');
            });
        }
    }
}
