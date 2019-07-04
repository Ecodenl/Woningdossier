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

                    print "Copying building data from building ".$building->id;

                    $newBuildingId = DB::table('buildings')->insertGetId($newBuildingData);
                    $building      = DB::table('buildings')->find($newBuildingId);

                    print "... to ".$building->id." (for user ".$user->id." != ".$originalUser->id.")".PHP_EOL;
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
                            if ($newEB instanceof stdClass) {
                                // There's a new example building, update the id
                                DB::table('buildings')
                                  ->where('id', '=', $building->id)
                                  ->update(['example_building_id' => $newEB->id]);
                            }
                        }
                    }
                } // example building updates

                // ------------- COPY BUILDING ELEMENTS ------------------------
                $this->copyTableDataForSiblings('building_elements',
                    $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING FEATURES ------------------------
                $this->copyTableDataForSiblings('building_features',
                    $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING HEATERS -------------------------
                $this->copyTableDataForSiblings('building_heaters',
                    $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING INSULATED GLAZINGS --------------
                $this->copyTableDataForSiblings('building_insulated_glazings',
                    $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING PAINTWORK STATUSE ---------------
                $this->copyTableDataForSiblings('building_paintwork_statuses',
                    $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING PV PANELS -----------------------
                $this->copyTableDataForSiblings('building_pv_panels',
                    $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING ROOF TYPES ----------------------
                $this->copyTableDataForSiblings('building_roof_types',
                    $originalBuilding->id, $building->id);

                // ------------- COPY BUILDING SERVICES ------------------------
                $this->copyTableDataForSiblings('building_services',
                    $originalBuilding->id, $building->id);

                // ------------- COPY QUESTIONS ANSWERS ------------------------
                $this->copyTableDataForSiblings('questions_answers',
                    $originalBuilding->id, $building->id);

                // ------------- COPY QUESTION TOOL SETTINGS -------------------
                $this->copyTableDataForSiblings('tool_settings',
                    $originalBuilding->id, $building->id);

                // ------------- COPY USER PROGRESSES --------------------------
                $this->copyTableDataForSiblings('user_progresses',
                    $originalBuilding->id, $building->id);

            }
        }
        // Some special tables (with > 1 foreign keys which should be updated)

        // get active buildings
        $buildings = DB::table('buildings')
                       ->whereNotNull('user_id')
                       ->whereNull('deleted_at')
                       ->get();

        foreach ($buildings as $building) {

            // building_coach_status
            $buildingCoachStatuses = DB::table('building_coach_statuses')
                                       ->whereNotNull('coach_id')
                                       ->where('building_id', '=',
                                           $building->id)
                                       ->get();

            foreach ($buildingCoachStatuses as $buildingCoachStatus) {

                $bcsBuilding = $buildingCoachStatus->building_id;
                $bcsCoach    = $buildingCoachStatus->coach_id;

                $updates = [];

                $allBuildings = $this->getBuildingSiblingPerCooperation($bcsBuilding);

                foreach ($allBuildings as $cooperationId => $stdBuilding) {
                    //dump("stdBuilding: ", $stdBuilding);
                    $updates[$cooperationId] = ['building_id' => $stdBuilding->id];
                }

                $allCoaches = $this->getUserSiblingPerCooperation($bcsCoach);

                foreach ($allCoaches as $cooperationId => $stdCoach) {
                    if (array_key_exists($cooperationId, $updates)) {
                        $updates[$cooperationId]['coach_id'] = $stdCoach->id;
                    }
                }

                foreach ($updates as $cooperationId => $replace) {
                    $insertBcs = (array) $buildingCoachStatus;
                    unset($insertBcs['id']);

                    $insertBcs['building_id'] = $replace['building_id'];
                    if ( ! array_key_exists('coach_id', $replace)) {
                        dump("No coach ID for cooperation ".$cooperationId.". Skipping.");
                        continue;
                    }
                    $insertBcs['coach_id'] = $replace['coach_id'];

                    DB::table('building_coach_statuses')->insert($insertBcs);
                }

                // remove original
                DB::table('building_coach_statuses')->where('id', '=',
                    $buildingCoachStatus->id)->delete();

            }

            // building_notes
            $buildingNotes = DB::table('building_notes')
                               ->whereNotNull('coach_id')
                               ->where('building_id', '=', $building->id)
                               ->get();

            foreach ($buildingNotes as $buildingNote) {

                $bnBuilding = $buildingNote->building_id;
                $bnCoach    = $buildingNote->coach_id;

                $updates = [];

                $allBuildings = $this->getBuildingSiblingPerCooperation($bnBuilding);

                foreach ($allBuildings as $cooperationId => $stdBuilding) {
                    $updates[$cooperationId] = ['building_id' => $stdBuilding->id];
                }

                $allCoaches = $this->getUserSiblingPerCooperation($bnCoach);

                foreach ($allCoaches as $cooperationId => $stdCoach) {
                    if (array_key_exists($cooperationId, $updates)) {
                        $updates[$cooperationId]['coach_id'] = $stdCoach->id;
                    }
                }

                foreach ($updates as $cooperationId => $replace) {
                    $insertBn = (array) $buildingNote;
                    unset($insertBn['id']);

                    $insertBn['building_id'] = $replace['building_id'];
                    if ( ! array_key_exists('coach_id', $replace)) {
                        dump("No coach ID for cooperation ".$cooperationId.". Removing.");
                        DB::table('building_notes')
                          ->where('id', '=', $buildingNote->id)
                          ->delete();
                        continue;
                    }
                    $insertBn['coach_id'] = $replace['coach_id'];

                    if ( ! DB::table('building_notes')
                             ->where('building_id', '=',
                                 $insertBn['building_id'])
                             ->where('coach_id', '=',
                                 $insertBn['coach_id'])->exists()) {
                        // only insert if it doesn't exist yet
                        DB::table('building_notes')->insert($insertBn);
                    }
                }

            } // foreach building notes

            // building_permissions
            $buildingPermissions = DB::table('building_permissions')
                                     ->where('building_id', '=', $building->id)
                                     ->get();
            foreach ($buildingPermissions as $buildingPermission) {
                $bpBuilding = $buildingPermission->building_id;
                $bpUser     = $buildingPermission->user_id;

                DB::table('building_permissions')->where('id', '=',
                    $buildingPermission->id)->delete();

                $updates = [];

                $allBuildings = $this->getBuildingSiblingPerCooperation($bpBuilding);

                foreach ($allBuildings as $cooperationId => $stdBuilding) {
                    $updates[$cooperationId] = ['building_id' => $stdBuilding->id];
                }

                $allUsers = $this->getUserSiblingPerCooperation($bpUser);
                foreach ($allUsers as $cooperationId => $stdUser) {
                    if (array_key_exists($cooperationId, $updates)) {
                        $updates[$cooperationId]['user_id'] = $stdUser->id;
                    }
                }

                foreach ($updates as $cooperationId => $permissions) {
                    if (array_key_exists('user_id',
                            $permissions) && array_key_exists('building_id',
                            $permissions)) {
                        if ( ! DB::table('building_permissions')
                                 ->where('building_id', '=',
                                     $permissions['building_id'])
                                 ->where('user_id', '=',
                                     $permissions['user_id'])->exists()) {
                            // only insert if it doesn't exist yet
                            DB::table('building_permissions')->insert($permissions);
                        }
                    }
                }
            }

            // private_messages
            $privateMessages = DB::table('private_messages')
                                 ->where('building_id', '=', $building->id)
                                 ->get();

            foreach ($privateMessages as $privateMessage) {
                $pmBuilding    = $privateMessage->building_id;
                $pmUser        = $privateMessage->from_user_id;
                $pmCooperation = $privateMessage->to_cooperation_id;


                $updates = [];

                $allBuildings = $this->getBuildingSiblingPerCooperation($pmBuilding);

                foreach ($allBuildings as $cooperationId => $stdBuilding) {
                    $updates[$cooperationId] = ['building_id' => $stdBuilding->id];
                }

                $allUsers = $this->getUserSiblingPerCooperation($pmUser);
                foreach ($allUsers as $cooperationId => $stdUser) {
                    if (array_key_exists($cooperationId, $updates)) {
                        $updates[$cooperationId]['from_user_id'] = $stdUser->id;
                    }
                }

                foreach ($updates as $cooperationId => $update) {
                    if ($cooperationId == $pmCooperation) {
                        if (array_key_exists('from_user_id', $update) &&
                            array_key_exists('building_id', $update)) {
                            $updateNeeded = DB::table('private_messages')
                                          ->where('id', '=',
                                              $privateMessage->id)
                                          ->where(function ($q) use ($update) {
                                              $q->where('building_id', '!=',
                                                  $update['building_id'])
                                                ->orWhere('from_user_id', '!=',
                                                    $update['from_user_id']);
                                          })->exists();
                            // only update if needed
                            if ($updateNeeded){
                                dump("Update needed for private message " . $privateMessage->id . " --> " . json_encode($update));
                                DB::table('private_messages')->where('id', '=', $privateMessage->id)
                                    ->update($update);
                            }
                        }
                    }
                }


            }




        } // foreach buildings

        //}
        dd("Done!");
    }


    protected function getUserSiblingPerCooperation($userId)
    {
        $result = [];

        $userId = (int) $userId;
        $user   = DB::table('users')->find($userId);

        if ($user instanceof stdClass) {

            $siblings = DB::table('users')->where('account_id', '=',
                $user->account_id)->get();

            foreach ($siblings as $sibling) {
                if ($sibling instanceof stdClass) {
                    $result[$sibling->cooperation_id] = $sibling;
                }
            }
        }

        return $result;
    }

    protected function getBuildingSiblingPerCooperation($buildingId)
    {
        $result = [];

        $buildingId = (int) $buildingId;
        $building   = DB::table('buildings')->find($buildingId);

        if ($building instanceof stdClass) {

            // Get the sibling of this building's user
            $users = $this->getUserSiblingPerCooperation($building->user_id);

            foreach ($users as $cooperation => $user) {

                $b = DB::table('buildings')->where('user_id',
                    '=', $user->id)->first();

                if ($b instanceof stdClass) {
                    $result[$cooperation] = $b;
                }
            }
        }

        return $result;
    }

    /**
     * Creates separate copies a table row for all buildings.
     *
     * @param  string  $table
     * @param  integer  $fromBuildingId
     * @param  integer  $toBuildingId
     * @param  string  $buildingColumn
     */
    protected function copyTableDataForSiblings(
        $table,
        $fromBuildingId,
        $toBuildingId,
        $buildingColumn = 'building_id'
    ) {
        $fromBuildingId = (int) $fromBuildingId;
        $toBuildingId   = (int) $toBuildingId;
        if ($fromBuildingId == $toBuildingId) {
            return;
        }

        $rows = DB::table($table)->where($buildingColumn, '=',
            $fromBuildingId)->get();
        /** @var stdClass $row */
        foreach ($rows as $row) {
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
