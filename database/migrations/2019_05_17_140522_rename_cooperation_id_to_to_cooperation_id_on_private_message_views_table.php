<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCooperationIdToToCooperationIdOnPrivateMessageViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_message_views', function (Blueprint $table) {
            $table->dropForeign(['cooperation_id']);
            $table->renameColumn('cooperation_id', 'to_cooperation_id');
            $table->foreign('to_cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_message_views', function (Blueprint $table) {
            $table->dropForeign(['to_cooperation_id']);
            $table->renameColumn('to_cooperation_id', 'cooperation_id');
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
        });
    }
}
