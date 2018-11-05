<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuildingIdToUserProgresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_progresses', function (Blueprint $table) {
            $table->integer('building_id')->unsigned()->nullable()->after('step_id');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
        });

        foreach (\App\Models\UserProgress::all() as $userProgress) {
            $building = \App\Models\Building::find($userProgress->user_id);
            $userProgress->building_id = $building->id;
            $userProgress->save();
        }

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
