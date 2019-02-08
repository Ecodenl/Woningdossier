<?php

use Illuminate\Database\Migrations\Migration;

class DeleteSleepingRoomWindowsFromElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // so we can get the id and remove it later on
        $sleepingRoomWindow = DB::table('elements')->where('short', 'sleeping-rooms-windows');

        if ($sleepingRoomWindow->first() instanceof stdClass) {
            // remove the user interests for the sleeping rooms windows
            DB::table('user_interests')
                ->where('interested_in_type', 'element')
                ->where('interested_in_id', $sleepingRoomWindow->first()->id)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
