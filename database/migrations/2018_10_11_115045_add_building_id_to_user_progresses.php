<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

        $progresses = DB::table('user_progresses')->get();
        foreach($progresses as $progress){
            $building = \DB::table('buildings')->where('user_id', '=', $progress->user_id)->get();
            DB::table('user_progresses')->update([ 'building_id' => $building->id ])->where('user_id', '=', $progress->user_id);
        }

        /*
        foreach (\App\Models\UserProgress::all() as $userProgress) {
            $building = \App\Models\Building::find($userProgress->user_id);
            $userProgress->building_id = $building->id;
            $userProgress->save();
        }
        */
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
