<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCooperationIdOnTablePrivateMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_messages', function (Blueprint $table) {
            $table->dropForeign(['cooperation_id']);
            $table->dropColumn('cooperation_id');
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
            $table->unsignedInteger('cooperation_id')->nullable()->default(null)->after('building_id');
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
        });

        foreach (DB::table('private_messages')->get() as $privateMessage) {
            DB::table('private_messages')->where('id', $privateMessage->id)->update([
                'cooperation_id' => $privateMessage->to_cooperation_id
            ]);
        }
    }
}
