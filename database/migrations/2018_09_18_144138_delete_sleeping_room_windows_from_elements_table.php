<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
        // remove the building elements
        DB::table('building_elements')->where('element_id', $sleepingRoomWindow->first()->id)->delete();
        // remove the max savings
        DB::table('building_type_element_max_savings')->where('element_id', $sleepingRoomWindow->first()->id)->delete();
        // remove the translation from the element itself
        DB::table('element_values')->where('element_id', $sleepingRoomWindow->first()->id)->delete();
        // remove the element
        $sleepingRoomWindow->delete();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
