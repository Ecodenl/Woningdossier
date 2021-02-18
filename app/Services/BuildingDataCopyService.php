<?php

namespace App\Services;

use App\Helpers\Str;
use App\Models\Building;
use App\Models\InputSource;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuildingDataCopyService
{
    /**
     * This methods copy data by deleting the destination its input, and inserting the source its data.
     *
     * @param Building $building
     * @param InputSource $from
     * @param InputSource $to
     */
    public static function deleteCopy(Building $building, InputSource $from, InputSource $to)
    {
        $tables = [
            'building_elements',
            'building_services',
            'user_interests',
            'user_action_plan_advices',
        ];

        foreach ($tables as $table) {

            Log::debug("Delete copying: table {$table}");

            // building to copy data from
            $user = $building->user()->first();

            // set the building or user id, depending on which column exists on the table
            if (\Schema::hasColumn($table, 'user_id')) {
                $buildingOrUserId = $user->id;
                $buildingOrUserColumn = 'user_id';
            } else {
                $buildingOrUserId = $building->id;
                $buildingOrUserColumn = 'building_id';
            }

            // get the input from the desired input source
            $fromValues = \DB::table($table)
                ->where('input_source_id', $from->id)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->get()->map(fn($from, $key) => static::createInsertFromSourceArray((array)$from, $to))->toArray();

            
            $valuesWhichWillBeDeleted = DB::table($table)
                ->where('input_source_id', $to->id)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->get()->toArray();

            if (!empty($valuesWhichWillBeDeleted)) {
                $columns = array_keys((array) $valuesWhichWillBeDeleted[0]);
                unset($columns[0]);
                $columns = '(' . implode(',', $columns) . ')';

                $valuesWhichWillBeDeleted = array_map(function ($value) {
                    $value = (array) $value;
                    unset($value['id']);
                    return '("' . implode('", "', $value) . '")';
                }, $valuesWhichWillBeDeleted);

                $insertData = implode(',', $valuesWhichWillBeDeleted) . ';';

                $sqlInsert = str_replace(
                    "\n",
                    "",
                    "
                        INSERT into {$table} {$columns}
                        values {$insertData}
                        "
                );

                // because of decimal column
                if ($table === 'user_action_plan_advices') {
                    str_replace('"",', '"0",', $sqlInsert);
                }

                $sqlDelete = "DELETE from {$table} where input_source_id = {$to->id} and {$buildingOrUserColumn} = {$buildingOrUserId}";

                // log the deleted data, this way we can easily go back if stuff goes south
                Log::debug("REVERSE FOR THE DELETE FOR {$table}");
                Log::debug($sqlDelete);
                Log::debug($sqlInsert);
            } else {
                Log::debug("NO DATA TO DELETE FOR {$table}");
            }

            // now delete the target its input
            DB::table($table)
                ->where('input_source_id', $to->id)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->delete();

            // and insert the data we want to copy
            DB::table($table)
                ->where('input_source_id', $to->id)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->insert($fromValues);

        }
    }

    private static function toRawSql($query)
    {
        return array_reduce($query->getBindings(), function ($sql, $binding) {
            return preg_replace('/\?/', is_numeric($binding) ? $binding : "'" . $binding . "'", $sql, 1);
        }, $query->toSql());
    }

    /**
     * This methods copies the data using an actual update, it does not delete the destination its input.
     *
     * @param Building $building
     * @param InputSource $from
     * @param InputSource $to
     */
    public static function hardCopy(Building $building, InputSource $from, InputSource $to)
    {
        // the tables that have a the where_column is used to query on the resident his answers.
        $tables = [
            'building_features',
            'building_paintwork_statuses',
            'building_ventilations',
            'building_pv_panels',
            'building_heaters',
            'building_appliances',
            'user_energy_habits',

            'building_roof_types' => [
                'where_column' => 'roof_type_id',
            ],
            'building_insulated_glazings' => [
                'where_column' => 'measure_application_id',
            ],
            'completed_steps' => [
                'where_column' => 'step_id',
            ],
            'questions_answers' => [
                'where_column' => 'question_id',
            ],
        ];

        foreach ($tables as $tableOrInt => $tableOrWhereColumns) {
            // if the $tableOrInt is an int the $tableOrWhereColumns contains a table, else it contains where columns which we will need later on.
            if (is_int($tableOrInt)) {
                $table = $tableOrWhereColumns;
            } else {
                $table = $tableOrInt;
            }
            Log::debug("Hard copy: table {$table}");


            if (\Schema::hasColumn($table, 'user_id')) {
                $buildingOrUserId = $building->user()->first()->id;
                $buildingOrUserColumn = 'user_id';
            } else {
                $buildingOrUserId = $building->id;
                $buildingOrUserColumn = 'building_id';
            }

            // now we get all the answers from the desired input source
            $fromValues = \DB::table($table)
                ->where('input_source_id', $from->id)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->get();

            // check if the $tableOrWhereColumns is a array and if a where column exists.
            // if so we need to add it to the query from the resident during the loop from the $fromValues
            if (is_array($tableOrWhereColumns) && array_key_exists('where_column', $tableOrWhereColumns)) {
                $whereColumn = $tableOrWhereColumns['where_column'];

                // loop through the answers from the desired input source
                foreach ($fromValues as $fromValue) {
                    if ($fromValue instanceof \stdClass && isset($fromValue->$whereColumn)) {
                        // now build the query to get the resident his answers
                        $toValueQuery = \DB::table($table)
                            ->where('input_source_id', $to->id)
                            ->where($buildingOrUserColumn, $buildingOrUserId)
                            ->where($whereColumn, $fromValue->$whereColumn);

                        $toValue = $toValueQuery->first();

                        static::updateOrInsert($to, $fromValue, $toValue, $toValueQuery, $buildingOrUserColumn, $buildingOrUserId);
                    }
                }
            } else {
                // get the resident his input
                $toValueQuery = \DB::table($table)
                    ->where('input_source_id', $to->id)
                    ->where($buildingOrUserColumn, $buildingOrUserId);

                // get the first result from the desired input source
                $fromValue = $fromValues->first();
                $toValue = $toValueQuery->first();

                static::updateOrInsert($to, $fromValue, $toValue, $toValueQuery, $buildingOrUserColumn, $buildingOrUserId);
            }
        }
    }

    /**
     * Method which will return insertable data, it updates/deletes a few params.
     *
     * @param array $fromData
     * @param InputSource $to
     * @return array
     */
    public static function createInsertFromSourceArray(array $fromData, InputSource $to): array
    {
        unset($fromData['id']);

        // if its empty we set the dates to now.
        $fromData['created_at'] = $fromData['created_at'] ?? Carbon::now()->toDateTimeString();
        $fromData['updated_at'] = $fromData['updated_at'] ?? Carbon::now()->toDateTimeString();
        $fromData['input_source_id'] = $to->id;

        if (array_key_exists('extra', $fromData)) {

            $extra = json_decode($fromData['extra'], true);
            if (!is_null($extra)) {
                $fromData['extra'] = static::filterExtraColumn($extra);
            }
            // some extra column contain "null", we dont want that
            if ($fromData['extra'] === "null") {
                $fromData['extra'] = [];
            }
            $fromData['extra'] = json_encode($fromData['extra']);
        }

        return $fromData;
    }

    /**
     * Method to updateOrInsert the source to the destination
     *
     * @param InputSource $to
     * @param \stdClass|null $fromValue
     * @param \stdClass|null $toValue
     * @param $toValueQuery
     * @param string $buildingOrUserColumn
     * @param string $buildingOrUserId
     */
    public static function updateOrInsert(InputSource $to, $fromValue, $toValue, $toValueQuery, string $buildingOrUserColumn, string $buildingOrUserId)
    {
        if ($toValue instanceof \stdClass) {
            // if the source has no valid data we dont do a update
            if (!empty($updateData = static::createUpdateArray((array)$toValue, (array)$fromValue))) {
                $toValueQuery->update($updateData);
            }
        } else {
            // unset the stuff we dont want to insert
            $fromValue = static::createUpdateArray((array)$toValue, (array)$fromValue);
            // change the input source id to the 'to' id
            $fromValue['input_source_id'] = $to->id;
            $fromValue[$buildingOrUserColumn] = $buildingOrUserId;

            // and insert a new row!
            \DB::table($toValueQuery->from)->insert($fromValue);
        }
    }

    /**
     * Method to copy data from a building and input source to a other input source on the same building.
     */
    public static function copy(Building $building, InputSource $from, InputSource $to)
    {
        $userId = \Auth::id();

        Log::debug("BUILDING_ID FOR COPY: {$building->id}");
        Log::debug("AUTH_USER_ID WHICH IS COPYING: {$userId}");

        static::hardCopy($building, $from, $to);
        static::deleteCopy($building, $from, $to);
    }

    /**
     * Check if a key / column name needs a update.
     *
     * @param string $key Column name
     *
     * @return bool
     */
    private static function keyNeedsUpdate($key)
    {
        $keysToNotUpdate = [
            'id', 'building_id', 'input_source_id', 'created_at', 'updated_at', 'comment', 'additional_info',
            'living_situation_extra',
        ];

        // if the key does exists in the array it does not need a update
        if (in_array($key, $keysToNotUpdate, true)) {
            return false;
        }

        return true;
    }

    /**
     * Returns whether or not fields are expressed as radio inputs. These fields
     * might have a value of 0, which is considered empty, but in the context of
     * radio buttons they should not be considered empty. We define these
     * fields here so we can test for !empty OR radio input.
     *
     * @param string $key
     *
     * @return bool
     */
    private static function isRadioInput($key)
    {
        return in_array($key, ['cavity_wall', 'monument', 'facade_plastered_painted']);
    }

    /**
     * Method to filter the value's from the extra column.
     *
     * @param $extraColumnData
     */
    private static function filterExtraColumn($extraColumnData): array
    {
        return array_filter($extraColumnData, function ($extraValue, $extraKey) {
            // if the string is not considered empty, and its need an update. Then we add it
            return !Str::isConsideredEmptyAnswer($extraValue) && static::keyNeedsUpdate($extraKey);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Creates an update array from the input source to copy and the input source to update.
     *
     * @param $inputSourceToUpdate
     * @param $inputSourceToCopy
     */
    private static function createUpdateArray($inputSourceToUpdate, $inputSourceToCopy): array
    {
        $updateArray = [];

        // if the desired input source has a extra key and its not empty, then we start to compare and merge the extra column.
        if (array_key_exists('extra', $inputSourceToCopy) && !empty($inputSourceToCopy['extra']) && is_array($inputSourceToCopy['extra'])) {
            if (empty($inputSourceToUpdate['extra'])) {
                $inputSourceToCopyExtra = json_decode($inputSourceToCopy['extra'], true);

                // filter the values which are not considered to be empty.
                $inputSourceToCopyNotNullExtraValues = static::filterExtraColumn($inputSourceToCopyExtra);

                $updateExtra = json_encode($inputSourceToCopyNotNullExtraValues);
            } else {
                $inputSourceToCopyExtra = json_decode($inputSourceToCopy['extra'], true);
                $inputSourceToUpdateExtra = json_decode($inputSourceToUpdate['extra'], true);

                $inputSourceToCopyNotNullExtraValues = static::filterExtraColumn($inputSourceToCopyExtra);

                // set some default stuff
                if (is_null($inputSourceToUpdateExtra)) {
                    $inputSourceToUpdateExtra = [];
                }

                // create the extra column json.
                // merge those toes
                $updateExtra = json_encode(array_merge($inputSourceToUpdateExtra, $inputSourceToCopyNotNullExtraValues));
            }

            // add the json to the extra ket
            $updateArray['extra'] = $updateExtra;
            // unset the id and extra, we dont need it anymore.
            unset($inputSourceToUpdate['id'], $inputSourceToCopy['extra']);
        }

        // now update the "normal" values
        foreach ($inputSourceToCopy as $desiredInputSourceKey => $desiredInputSourceAnswer) {
            // if the answer from the desired input source is not empty and the key needs a update, then we update the resident his answer.
            if ((!empty($desiredInputSourceAnswer) || static::isRadioInput($desiredInputSourceKey)) && static::keyNeedsUpdate($desiredInputSourceKey)) {
                $updateArray[$desiredInputSourceKey] = $desiredInputSourceAnswer;
            }
        }

        $updateArray['updated_at'] = Carbon::now()->toDateTimeString();
        return $updateArray;
    }
}
