<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNotificationSettingsAndLogsToUserAccountStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notificationSettings = DB::table('notification_settings')->get();
        foreach ($notificationSettings as $notificationSetting) {
            $siblings = $this->getUserSiblingPerCooperation($notificationSetting->user_id);
            foreach ($siblings as $sibling) {
                /** @var stdClass $sibling */
                $this->copyTableDataForSiblings('notification_settings',
                    $notificationSetting->user_id, $sibling->id);
            }
        }

        $logs = DB::table('logs')->get();
        foreach ($logs as $log) {
            $hasSiblings = $this->hasSiblings($log->building_id);
            // only if a building has siblings we need to check data consistency
            if ($hasSiblings) {

                $stuff     = [];
                $buildings = $this->getBuildingSiblingPerCooperation($log->building_id);

                foreach ($buildings as $cooperationId => $building) {
                    if ( ! array_key_exists($cooperationId, $stuff)) {
                        $stuff[$cooperationId] = ['building' => $building];
                    }
                }

                $actors = $this->getUserSiblingPerCooperation($log->user_id);
                foreach ($actors as $cooperationId => $actor) {
                    if (array_key_exists($cooperationId, $stuff)) {
                        $stuff[$cooperationId]['user'] = $actor;
                    }
                }

                $fors = $this->getUserSiblingPerCooperation($log->for_user_id);
                foreach ($fors as $cooperationId => $for) {
                    if (array_key_exists($cooperationId, $stuff)) {
                        $stuff[$cooperationId]['for'] = $for;
                    }
                }

                foreach ($stuff as $data) {
                    if (array_key_exists('building', $data) && array_key_exists('user', $data)){
                        $copy = (array) $log;
                        unset($copy['id']);

                        $copy['building_id'] = $data['building']->id;
                        $copy['user_id'] = $data['user']->id;
                        if (array_key_exists('for', $data)){
                            $copy['for_user_id'] = $data['for']->id;
                        }

                        DB::table('logs')->updateOrInsert($copy, $copy);
                    }
                }
                // Remove the old record
                DB::table('logs')->delete($log->id);

            }
        }
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

    protected function hasSiblings($buildingId)
    {
        return count($this->getBuildingSiblingPerCooperation($buildingId)) > 1;
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
     * @param  integer  $fromUserId
     * @param  integer  $toUserId
     * @param  string  $userColumn
     */
    protected function copyTableDataForSiblings(
        $table,
        $fromUserId,
        $toUserId,
        $userColumn = 'user_id'
    ) {
        $fromUserId = (int) $fromUserId;
        $toUserId   = (int) $toUserId;
        if ($fromUserId == $toUserId) {
            return;
        }

        $rows = DB::table($table)->where($userColumn, '=',
            $fromUserId)->get();
        /** @var stdClass $row */
        foreach ($rows as $row) {
            $row = (array) $row;
            unset($row['id']);
            $row[$userColumn] = $toUserId;

            DB::table($table)->updateOrInsert([$userColumn => $row[$userColumn]], $row);
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
