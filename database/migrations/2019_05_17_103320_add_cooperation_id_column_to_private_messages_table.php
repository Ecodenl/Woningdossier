<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCooperationIdColumnToPrivateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_messages', function (Blueprint $table) {
            $table->unsignedInteger('cooperation_id')->nullable()->default(null)->after('building_id');
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_messages', function (Blueprint $table) {
            $table->dropForeign(['cooperation_id']);
            $table->dropColumn('cooperation_id');
        });
    }
}
