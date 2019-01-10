<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserProgressUserIdToInputSourceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_progresses', function (Blueprint $table) {

            // drop the foreign key
            $table->dropForeign(['user_id']);
            // might drop the column aswell
            $table->dropColumn('user_id');

            // add the input source id
            $table->integer('input_source_id')->unsigned()->nullable()->after('id');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('set null');

        });

        // seed the data
        Schema::table('user_progresses', function (Blueprint $table) {
            // get the resident input source
            $residentInputSource = DB::table('input_sources')->where('short', 'resident')->first();
            // we just give them all the resident input source
            DB::table('user_progresses')->update(['input_source_id' => $residentInputSource->id]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_progresses', function (Blueprint $table) {

            // drop the foreign
            $table->dropForeign(['input_source_id']);
            // rename it back
            $table->dropColumn('input_source_id');


            $table->integer('user_id')->unsigned()->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });

        // seed
        Schema::table('user_progresses', function (Blueprint $table) {

            // get the data back
            // get the user_ids with the buildingid
            $userAndBuildingIds = \DB::table('user_progresses')
                ->join('buildings', 'buildings.id', '=', 'user_progresses.building_id')
                ->select('buildings.user_id', 'user_progresses.building_id')
                ->get();

            // now update the zooi back
            foreach ($userAndBuildingIds as $userAndBuildingId) {
                $userAndBuildingId = (array) $userAndBuildingId;
                \DB::table('user_progresses')->where('building_id', $userAndBuildingId['building_id'])->update($userAndBuildingId);
            }

        });


    }
}
