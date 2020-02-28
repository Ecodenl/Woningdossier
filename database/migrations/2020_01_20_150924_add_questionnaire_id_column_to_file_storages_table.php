<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuestionnaireIdColumnToFileStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_storages', function (Blueprint $table) {
            $table->unsignedInteger('questionnaire_id')->after('building_id')->nullable()->default(null);
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_storages', function (Blueprint $table) {
            $table->dropForeign(['questionnaire_id']);
            $table->dropColumn('questionnaire_id');
        });
    }
}
