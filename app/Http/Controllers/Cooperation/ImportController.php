<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Models\Element;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Services\ToolSettingService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    /**
     * Check if a key / column name needs a update
     *
     * @param $key | Column name
     * @return bool
     */
    private function keyNeedsUpdate($key)
    {
        $keysToNotUpdate = ['id', 'building_id', 'input_source_id', 'created_at', 'updated_at', 'comment'];
        if (!in_array($key, $keysToNotUpdate, true)) {
            return true;
        }
        return false;
    }

    /**
     * Creates an update array from the input source to copy and the input source to update
     *
     * @param $inputSourceToUpdate
     * @param $inputSourceToCopy
     * @return array
     */
    private function createUpdateArray($inputSourceToUpdate, $inputSourceToCopy): array
    {

        $updateArray = [];

        // if the desired input source has a extra key and its not empty, then we start to compare and merge the extra column.
        if (array_key_exists('extra', $inputSourceToCopy) && !empty($inputSourceToCopy['extra'])) {

            // if the resident had nothing then we just use the desired input source extra.
            if (empty($inputSourceToUpdate['extra'])) {
                $updateExtra = $inputSourceToCopy['extra'];
            } else {
                $inputSourceToCopyExtra = json_decode($inputSourceToCopy['extra'], true);
                $inputSourceToUpdateExtra = json_decode($inputSourceToUpdate['extra'], true);


                // filter the values which are not considered to be empty.
                $inputSourceToCopyNotNullExtraValues = array_filter($inputSourceToCopyExtra, function ($extraValue, $extraKey) {
                    // if the string is not considered empty, then we want to update it.
                    return !Str::isConsideredEmptyAnswer($extraValue) && $this->keyNeedsUpdate($extraKey);
                }, ARRAY_FILTER_USE_BOTH);

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
            if (!empty($desiredInputSourceAnswer) && $this->keyNeedsUpdate($desiredInputSourceKey)) {
                $updateArray[$desiredInputSourceKey] = $desiredInputSourceAnswer;
            }
        }

        return $updateArray;
    }

    public function copy(Request $request)
    {
        $desiredInputSourceName = $request->get('input_source');

        // the tables that have a the where_column is used to query on the resident his answers.
        $tables = [
            'building_features',
            'building_elements' => [
                'where_column' => 'element_id',
                'additional_where_column' => 'element_value_id'
            ],
            'building_services' => [
                'where_column' => 'service_id',
                'additional_where_column' => 'service_value_id'
            ],
            'building_roof_types' => [
                'where_column' => 'roof_type_id'
            ],
            'building_insulated_glazings' => [
                'where_column' => 'measure_application_id'
            ],
            'building_user_usages',
            'building_paintwork_statuses',
            'building_user_usages',
            'user_progresses',
            'questions_answers',
            'building_features',
            'building_pv_panels',
            'building_heaters',
            'building_appliances',

            'user_action_plan_advices' => [
                'where_column' => 'measure_application_id'
            ],
            'user_energy_habits',
            'user_interests' => [
                'where_column' => 'interested_in_type',
                'additional_where_column' => 'interested_in_id'
            ]

        ];
        
        // input sources
        $desiredInputSource = InputSource::findByShort($desiredInputSourceName);
        $residentInputSource = InputSource::findByShort('resident');

        foreach ($tables as $tableOrInt => $tableOrWhereColumns) {

            // now check if its a int
            // if it isn't a int, the $tableOrId is a table and the $tableOrWhereColumns is a where column
            // else the $tableOrWhereColumns is the table and we do not need to query further.
            if (!is_int($tableOrInt)) {
                $table = $tableOrInt;
                $whereColumn = $tableOrWhereColumns['where_column'];
            } else {
                $table = $tableOrWhereColumns;
            }
            
            // set the building or user id, depending on which column exists on the table
            if (\Schema::hasColumn($table, 'user_id')) {
                $buildingOrUserId = \Auth::id();
                $buildingOrUserColumn = 'user_id';
            } else {
                $buildingOrUserId = HoomdossierSession::getBuilding();
                $buildingOrUserColumn = 'building_id';
            }

            // now we get all the answers from the desired input source
            $desiredInputSourceValues = \DB::table($table)
                ->where('input_source_id', $desiredInputSource->id)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->get();


            // now check if the $whereColumn isset
            // if so we need to add it to the query from the resident during the loop from the $desiredInputSourceValues
            if (isset($whereColumn)) {

                // loop through the answers from the desired input source
                foreach ($desiredInputSourceValues as $desiredInputSourceValue) {
                    if ($desiredInputSourceValue instanceof \stdClass && isset($desiredInputSourceValue->$whereColumn)) {

                        // now build the query to get the resident his answers
                        $residentInputSourceValueQuery = \DB::table($table)
                            ->where('input_source_id', $residentInputSource->id)
                            ->where($buildingOrUserColumn, $buildingOrUserId)
                            ->where($whereColumn, $desiredInputSourceValue->$whereColumn);


                        // count the rows
                        $residentInputSourceValueCount = \DB::table($table)
                            ->where('input_source_id', $residentInputSource->id)
                            ->where($buildingOrUserColumn, $buildingOrUserId)
                            ->where($whereColumn, $desiredInputSourceValue->$whereColumn)
                            ->count();

                        // if there are multiple, then we need to add another where to the query.
                        // else, we dont need to query further an can get the first result and use that to update it.
                        if ($residentInputSourceValueCount > 1) {

                            $additionalWhereColumn = $tableOrWhereColumns['additional_where_column'];
                            // add the where to the query
                            $residentInputSourceValueQuery = $residentInputSourceValueQuery
                                ->where($additionalWhereColumn, $desiredInputSourceValue->$additionalWhereColumn);

                            // get the result
                            $residentInputSourceValue = $residentInputSourceValueQuery->first();

                            // cast the results to a array
                            $residentInputSourceValue = (array)$residentInputSourceValue;
                            $desiredInputSourceValue = (array)$desiredInputSourceValue;

                            // if it exists, we need to update it. Else we need to insert a new row.
                            if (!empty($residentInputSourceValue)) {
                                $residentInputSourceValueQuery->update($this->createUpdateArray($residentInputSourceValue, $desiredInputSourceValue));
                            } else {
                                // unset the stuff we dont want to insert
                                unset($desiredInputSourceValue['id'], $desiredInputSourceValue['input_source_id']);
                                // change the input source id to the resident
                                $desiredInputSourceValue['input_source_id'] = $residentInputSource->id;
                                // and insert a new row!
                                \DB::table($table)->insert($desiredInputSourceValue);
                            }
                        } else {

                            $residentInputSourceValue = $residentInputSourceValueQuery->first();
                            // cast the results to a array
                            $residentInputSourceValue = (array)$residentInputSourceValue;
                            $desiredInputSourceValue = (array)$desiredInputSourceValue;

                            // YAY! data has been copied so update the resident his records.
                            $residentInputSourceValueQuery->update($this->createUpdateArray($residentInputSourceValue, $desiredInputSourceValue));
                        }
                    }
                }
            } else {
                // get the resident his input
                $residentInputSourceValueQuery = \DB::table($table)
                    ->where('input_source_id', $residentInputSource->id)
                    ->where($buildingOrUserColumn, $buildingOrUserId);

                // get the first result from the desired input source
                $desiredInputSourceValue = $desiredInputSourceValues->first();
                $residentInputSourceValue = $residentInputSourceValueQuery->first();

                // if it exists, we need to update it. Else we need to insert a new row.
                if ($residentInputSourceValue instanceof \stdClass) {
                    $residentInputSourceValueQuery->update($this->createUpdateArray((array) $desiredInputSource, (array) $desiredInputSourceValue));
                } else {
                    $desiredInputSourceValue = (array) $desiredInputSourceValue;
                    // unset the stuff we dont want to insert
                    unset($desiredInputSourceValue['id'], $desiredInputSourceValue['input_source_id']);
                    // change the input source id to the resident
                    $desiredInputSourceValue['input_source_id'] = $residentInputSource->id;
                    // and insert a new row!
                    \DB::table($table)->insert($desiredInputSourceValue);
                }

            }
        }


        ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $desiredInputSource->id, false);
        HoomdossierSession::stopUserComparingInputSources();

        return redirect()->back();
    }

}
