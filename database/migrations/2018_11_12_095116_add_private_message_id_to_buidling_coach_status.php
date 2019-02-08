<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrivateMessageIdToBuidlingCoachStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('building_coach_statuses', 'private_message_id')) {
            Schema::table('building_coach_statuses',
                function (Blueprint $table) {
                    $table->integer('private_message_id')->unsigned()->nullable();
                    $table->foreign('private_message_id')->references('id')->on('private_messages')->onDelete('set null');
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
        // We don't know for sure if the migration has run, so don't do anything.
    }
}
