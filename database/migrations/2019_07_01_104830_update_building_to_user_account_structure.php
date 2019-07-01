<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBuildingToUserAccountStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // get active buildings
        $buildings = DB::table('buildings')
                       ->whereNotNull('user_id')
                       ->whereNull('deleted_at')
                       ->get();

        foreach ($buildings as $building) {
            // get all siblings for the building's user:
            // 1: get building user
            $originalUser = DB::table('users')->where('id', '=',
                $building->user_id)->first();
            if ( ! $originalUser instanceof stdClass) {
                print "No user found for building ".$building->id.PHP_EOL;

                return;
            }
            // 2: get all users with the same account
            $users = DB::table('users')
                       ->where('account_id', '=', $originalUser->account_id)
                       ->get();

            $originalBuilding = $building;

            foreach ($users as $user) {
                // note every $user has a different cooperation_id now!

                // we do everything in two steps:
                // 1: copy data
                // 2: update where necessary

                // ------------- COPY BUILDING ---------------------------------
                if ($user->id != $originalUser->id) {
                    $newBuildingData            = (array) $building;
                    $newBuildingData['user_id'] = $user->id;
                    unset($newBuildingData['id']);
                    $newBuildingId = DB::table('buildings')->insertGetId($newBuildingData);
                    $building      = DB::table('buildings')->find($newBuildingId);
                }

                // Check if the example building id is not specific or allowed for the current cooperation
                $eb = $building->example_building_id;

                if ( ! is_null($eb)) {
                    $exampleBuilding = DB::table('example_buildings')->find($eb);
                    if ($exampleBuilding instanceof stdClass) {
                        if ( ! is_null($exampleBuilding->cooperation_id) && $exampleBuilding->cooperation_id != $user->cooperation_id) {

                            dump("Building ".$building->id." has user with cooperation_id ".$user->cooperation_id." , but example building for cooperation_id ".$exampleBuilding->cooperation_id);

                            // Just select the "non-specific"
                            $newEB = DB::table('example_buildings')
                                       ->where('building_type_id', '=',
                                           $building->building_type_id)
                                       ->whereNull('cooperation_id')
                                       ->first();
                            if ($newEB instanceof stdClass){
                                // There's a new example building, update the id
                                DB::table('buildings')
                                  ->where('id', '=', $building->id)
                                  ->update(['example_building_id' => $newEB->id]);
                            }
                        }
                    }
                } // example building updates

                // ------------- COPY BUILDING ELEMENTS ------------------------
                $this->copyTableDataForSiblings('building_elements', $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING FEATURES ------------------------
                $this->copyTableDataForSiblings('building_features', $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING HEATERS -------------------------
                $this->copyTableDataForSiblings('building_heaters', $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING INSULATED GLAZINGS --------------
                $this->copyTableDataForSiblings('building_insulated_glazings', $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING PAINTWORK STATUSE ---------------
                $this->copyTableDataForSiblings('building_paintwork_statuses', $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING PV PANELS -----------------------
                $this->copyTableDataForSiblings('building_pv_panels', $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING ROOF TYPES ----------------------
                $this->copyTableDataForSiblings('building_roof_types', $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING SERVICES ------------------------
                $this->copyTableDataForSiblings('building_services', $originalBuilding->id, $building->id);

                // ------------- COPY QUESTION ANSWERS -------------------------
                $this->copyTableDataForSiblings('question_answers', $originalBuilding->id, $building->id);

                // ------------- COPY QUESTION TOOL SETTINGS -------------------
                $this->copyTableDataForSiblings('tool_settings', $originalBuilding->id, $building->id);

                // ------------- COPY USER PROGRESSES --------------------------
                $this->copyTableDataForSiblings('user_progresses', $originalBuilding->id, $building->id);


                // Some special tables (with > 1 foreign keys which should be updated)


            }



        }

    }

    /**
     * Creates separate copies a table row for all buildings.
     *
     * @param string $table
     * @param integer $fromBuildingId
     * @param integer $toBuildingId
     * @param string $buildingColumn
     */
    protected function copyTableDataForSiblings($table, $fromBuildingId, $toBuildingId, $buildingColumn = 'building_id')
    {
        $fromBuildingId = (int) $fromBuildingId;
        $toBuildingId = (int) $toBuildingId;
        if ($fromBuildingId == $toBuildingId){
            return;
        }

        $rows = DB::table($table)->where($buildingColumn, '=', $fromBuildingId)->get();
        /** @var stdClass $row */
        foreach($rows as $row){
            $row = (array) $row;
            unset($row['id']);
            $row[$buildingColumn] = $toBuildingId;

            DB::table($table)->insert($row);
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
