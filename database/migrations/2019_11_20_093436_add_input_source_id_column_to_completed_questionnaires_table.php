<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInputSourceIdColumnToCompletedQuestionnairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('completed_questionnaires', function (Blueprint $table) {
            $table->unsignedInteger('input_source_id')->nullable()->default(null)->after('user_id');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('completed_questionnaires', function (Blueprint $table) {
            $table->dropForeign(['input_source_id']);
            $table->dropColumn('input_source_id');
        });
    }
}
