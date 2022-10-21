<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditableColumnsToMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->string('description')->nullable()->after('title');

            $table->unsignedInteger('input_source_id')->nullable()->after('description');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['input_source_id']);
            $table->dropColumn(['title', 'description', 'input_source_id']);
        });
    }
}
