<?php

namespace App\Services;

use App\Helpers\ObjectHelper;
use App\Helpers\Str;
use App\Models\Building;
use App\Models\InputSource;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class BuildingDataCopyService
{
    /**
     * Set the master input sources for a building in all relevant tables
     *
     * @param  \App\Models\Building  $building
     */
    public static function setMasterInputSources(Building $building)
    {
        $tables = [
            'building_elements' => [
                'where_column' => 'element_id',
                'answer_columns' => [
                    'element_value_id',
                    'extra',
                ],
            ],
            'building_services' => [
                'where_column' => 'service_id',
                'answer_columns' => [
                    'service_value_id',
                    'extra',
                ],
            ],
            'user_interests' => [
                'where_column' => 'interested_in_type',
                'additional_where_column' => 'interested_in_id',
                'answer_columns' => [
                    'interest_id',
                ],
            ],
            'user_action_plan_advices' => [
                'where_column' => 'step_id',
                'additional_where_column' => 'measure_application_id',
                'answer_columns' => [
                    'costs',
                    'savings_gas',
                    'savings_electricity',
                    'savings_money',
                    'year',
                    'planned',
                    'planned_year',
                ],
            ],
            'building_features' => [
                'answer_columns' => [
                    'building_heating_application_id',
                    'building_category_id',
                    'building_type_id',
                    'roof_type_id',
                    'energy_label_id',
                    'cavity_wall',
                    'wall_surface',
                    'insulation_wall_surface',
                    'facade_plastered_painted',
                    'wall_joints',
                    'contaminated_wall_joints',
                    'element_values',
                    'facade_plastered_surface_id',
                    'facade_damaged_paintwork_id',
                    'surface',
                    'floor_surface',
                    'insulation_surface',
                    'window_surface',
                    'volume',
                    'build_year',
                    'building_layers',
                    'monument',
                ],
            ],
            'building_paintwork_statuses' => [
                'answer_columns' => [
                    'last_painted_year',
                    'paintwork_status_id',
                    'wood_rot_status_id',
                ],
            ],
            'building_ventilations' => [
                'answer_columns' => [
                    'how',
                    'living_situation',
                    'usage',
                ],
            ],
            'building_pv_panels' => [
                'answer_columns' => [
                    'total_installed_power',
                    'peak_power',
                    'number',
                    'pv_panel_orientation_id',
                    'angle',
                ],
            ],
            'building_heaters' => [
                'answer_columns' => [
                    'pv_panel_orientation_id',
                    'angle',
                ],
            ],
            'building_appliances' => [
                'answer_columns' => [
                    'appliance_id',
                ],
            ],
            'user_energy_habits' => [
                'answer_columns' => [
                    'resident_count',
                    'thermostat_high',
                    'thermostat_low',
                    'hours_high',
                    'heating_first_floor',
                    'heating_second_floor',
                    'heated_space_outside',
                    'cook_gas',
                    'water_comfort_id',
                    'amount_electricity',
                    'amount_gas',
                    'amount_water',
                    'renovation_plans',
                    'building_complaints',
                    'start_date',
                    'end_date',
                ],
            ],
            'building_roof_types' => [
                'where_column' => 'roof_type_id',
                'answer_columns' => [
                    'element_value_id',
                    'roof_surface',
                    'insulation_roof_surface',
                    'zinc_surface',
                    'building_heating_id',
                ],
            ],
            'building_insulated_glazings' => [
                'where_column' => 'measure_application_id',
                'answer_columns' => [
                    'insulating_glazing_id',
                    'building_heating_id',
                    'm2',
                    'windows',
                    'extra',
                ],
            ],
            'questions_answers' => [
                'where_column' => 'question_id',
                'answer_columns' => [
                    'answer',
                ],
            ],
        ];

        // Get the input sources
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);
        $residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        foreach ($tables as $table => $tableData) {
            Log::debug(__METHOD__ . " -> Processing {$table} for building {$building->id}");

            if (Schema::hasColumn($table, 'user_id')) {
                $buildingOrUserId = $building->user->id;
                $buildingOrUserColumn = 'user_id';
            } else {
                $buildingOrUserId = $building->id;
                $buildingOrUserColumn = 'building_id';
            }

            // Get all values for the current building for resident and coach input source
            $values = DB::table($table)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->whereIn('input_source_id', [$coachInputSource->id, $residentInputSource->id])
                ->get();

            if ($values->isNotEmpty()) {
                $answerColumns = $tableData['answer_columns'];
                $whereColumn = $tableData['where_column'] ?? null;
                $additionalWhereColumn = $tableData['additional_where_column'] ?? null;

                $differentiatingValues = [];
                $differentiatingSubValues = [];

                // Set conditional values
                if (! is_null($whereColumn)) {
                    // There is a where column. This means multiple values per input source. Let's grab them
                    $differentiatingValues = $values->pluck($whereColumn)->unique()->toArray();

                    if (! is_null($additionalWhereColumn)) {
                        // There is an additional column... Let's grab these too
                        foreach ($differentiatingValues as $differentiatingValue) {
                            $differentiatingSubValues[$differentiatingValue] = $values
                                ->where($whereColumn, $differentiatingValue)
                                ->pluck($additionalWhereColumn)->unique()->toArray();
                        }
                    }
                }

                $masterInputSourceAnswers = [];

                // We will loop all the answer columns, as we must check them individually
                foreach ($answerColumns as $answerColumn) {
                    if (! is_null($whereColumn)) {
                        foreach ($differentiatingValues as $differentiatingValue) {
                            if (! is_null($additionalWhereColumn)) {
                                foreach ($differentiatingSubValues[$differentiatingValue] as $differentiatingSubValue) {
                                    // Grab the answer of the coach
                                    $coachAnswer = static::searchCollectionForValue($values, $coachInputSource,
                                        [
                                            $whereColumn => $differentiatingValue,
                                            $additionalWhereColumn => $differentiatingSubValue,
                                        ]);

                                    $answer = ObjectHelper::getObjectProperty($coachAnswer, $answerColumn);

                                    if (empty($answer) && ! is_numeric($answer)) {
                                        // Grab the answer of the resident if answer is TRULY empty
                                        $residentAnswer = static::searchCollectionForValue($values, $residentInputSource,
                                            [
                                                $whereColumn => $differentiatingValue,
                                                $additionalWhereColumn => $differentiatingSubValue,
                                            ]);

                                        $answer = ObjectHelper::getObjectProperty($residentAnswer, $answerColumn);
                                    }

                                    // Build answer structure with where and additional where
                                    $masterInputSourceAnswers[$answerColumn][$whereColumn][$differentiatingValue][$additionalWhereColumn][$differentiatingSubValue] = $answer;
                                }
                            } else {
                                // Grab the answer of the coach
                                $coachAnswer = static::searchCollectionForValue($values, $coachInputSource,
                                    [$whereColumn => $differentiatingValue]);

                                $answer = ObjectHelper::getObjectProperty($coachAnswer, $answerColumn);

                                if (empty($answer) && ! is_numeric($answer)) {
                                    // Grab the answer of the resident if answer is TRULY empty
                                    $residentAnswer = static::searchCollectionForValue($values, $residentInputSource,
                                        [$whereColumn => $differentiatingValue]);

                                    $answer = ObjectHelper::getObjectProperty($residentAnswer, $answerColumn);
                                }

                                // Build answer structure with where
                                $masterInputSourceAnswers[$answerColumn][$whereColumn][$differentiatingValue] = $answer;
                            }
                        }
                    } else {
                        // Grab the answer of the coach
                        $coachAnswer = static::searchCollectionForValue($values, $coachInputSource);

                        $answer = ObjectHelper::getObjectProperty($coachAnswer, $answerColumn);

                        if (empty($answer) && ! is_numeric($answer)) {
                            // Grab the answer of the resident if answer is TRULY empty
                            $residentAnswer = static::searchCollectionForValue($values, $residentInputSource);

                            $answer = ObjectHelper::getObjectProperty($residentAnswer, $answerColumn);
                        }

                        // Build default answer structure
                        $masterInputSourceAnswers[$answerColumn] = $answer;
                    }
                }

                // We now have the structure for the row(s) of answers we need to put under the master input source
                // Structure is default column => answer
                // With one where: column => [whereColumn => [whereColumnValue => answer]]
                // With additional where: column => [whereColumn => [whereColumnValue => additionalWhereColumn => [additionalWhereColumnValue => answer]]]

                // Default logic
                $baseUpdateOrInsertLogic = [
                    $buildingOrUserColumn => $buildingOrUserId,
                    'input_source_id' => $masterInputSource->id
                ];

                if (is_null($whereColumn)) {
                    // Default structure, easy pickins!
                    $answersToInsert = $masterInputSourceAnswers;
                    DB::table($table)
                        ->updateOrInsert($baseUpdateOrInsertLogic, $answersToInsert);
                } else {
                    if (is_null($additionalWhereColumn)) {
                        // Only where column
                        foreach ($differentiatingValues as $differentiatingValue) {
                            $answersToInsert = [];

                            // Set answers
                            foreach ($masterInputSourceAnswers as $answerColumn => $answers) {
                                $answersToInsert[$answerColumn] = $answers[$whereColumn][$differentiatingValue];
                            }

                            // Set custom logic for insert
                            $customLogic = $baseUpdateOrInsertLogic;
                            $customLogic[$whereColumn] = $differentiatingValue;

                            // Insert for each where
                            DB::table($table)
                                ->updateOrInsert($customLogic, $answersToInsert);
                        }
                    } else {
                        // With additional where column
                        foreach ($differentiatingValues as $differentiatingValue) {
                            foreach ($differentiatingSubValues[$differentiatingValue] as $differentiatingSubValue) {
                                $answersToInsert = [];

                                // Set answers
                                foreach ($masterInputSourceAnswers as $answerColumn => $answers) {
                                    $answersToInsert[$answerColumn] = $answers[$whereColumn][$differentiatingValue][$additionalWhereColumn][$differentiatingSubValue];
                                }

                                // Set custom logic for insert
                                $customLogic = $baseUpdateOrInsertLogic;
                                $customLogic[$whereColumn] = $differentiatingValue;
                                $customLogic[$additionalWhereColumn] = $differentiatingSubValue;

                                // Insert for each where & additional where
                                DB::table($table)
                                    ->updateOrInsert($customLogic, $answersToInsert);
                            }
                        }
                    }
                }
            } else {
                Log::alert(__METHOD__ . " -> No values for table {$table} for building {$building->id}!");
            }
        }
    }

    /**
     * Find a value in a collection related to an input source, with potentially extra columns to check
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  \App\Models\InputSource  $inputSource
     * @param  array  $extra
     *
     * @return mixed
     */
    public static function searchCollectionForValue(Collection $collection, InputSource $inputSource, array $extra = [])
    {
        $search = $collection->where('input_source_id', $inputSource->id);

        foreach ($extra as $column => $value) {
            $search = $search->where($column, $value);
        }

        return $search->first();
    }

    /**
     * This method copies using a delete method, it does not care about the target its data.
     *
     * If the source has data the target does not have, we copy it to the target
     * if the source has the same data as the target, we replace the target its value
     *
     * @param Building $building
     * @param InputSource $from
     * @param InputSource $to
     */
    public static function deleteCopy(Building $building, InputSource $source, InputSource $target)
    {
        $tables = [
            'building_elements' => [
                'where_column' => 'element_id',
            ],
            'building_services' => [
                'where_column' => 'service_id',
            ],
            'user_interests' => [
                'where_column' => 'interested_in_type',
                'additional_where_column' => 'interested_in_id',
            ],
            'user_action_plan_advices' => [
                'where_column' => 'step_id',
                'additional_where_column' => 'measure_application_id',
            ]
        ];

        // check if we need to update or insert
        // this seems easy, however we have multiple things that we need to keep in mind.
        // the source could have less data than the target, in that case we keep the target its data.
        // the source could have the same data as the target, that case we will update the target its input.
        // the source could have more data than the target, that case we will insert the missing data
        // but we also need to keep in mind the way data is stored eg; element_id & element_value_id.
        foreach ($tables as $table => $tableColumns) {

            $whereColumn = $tableColumns['where_column'];
            $additionalWhereColumn = $tableColumns['additional_where_column'] ?? null;
            // building to copy data from
            $user = $building->user()->first();

            // set the building or user id, depending on which column exists on the table
            if (Schema::hasColumn($table, 'user_id')) {
                $buildingOrUserId = $user->id;
                $buildingOrUserColumn = 'user_id';
            } else {
                $buildingOrUserId = $building->id;
                $buildingOrUserColumn = 'building_id';
            }

            $targetValues = DB::table($table)
                ->where('input_source_id', $target->id)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->get();


            $sourceValues = DB::table($table)
                ->where('input_source_id', $source->id)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->get();

            // if the target has no values at all, we can just do a plain copy without any crap in between
            if ($targetValues->isEmpty()) {
                // transform the source values to a insertable format for the target.
                $insertableTargetValues = $sourceValues
                    ->map(fn($sourceValue, $key) => static::createInsertFromSourceArray((array)$sourceValue, $target))
                    ->toArray();

                DB::table($table)->insert($insertableTargetValues);
            } else {

                foreach ($sourceValues as $sourceValue) {
                    // get the possible targets, we will remove them and insert the source values instead
                    $possibleTargetValues = self::getPossibleTargetValues($sourceValue, $targetValues, $whereColumn, $additionalWhereColumn);

                    foreach ($possibleTargetValues as $possibleTargetValue) {
                        DB::table($table)->where('id', $possibleTargetValue->id)->delete();
                    }

                    // and insert the source values.
                    DB::table($table)->insert(static::createInsertFromSourceArray((array)$sourceValue, $target));
                }
            }
        }
    }

    /**
     * Returns the targets which should be delete copied
     */
    public static function getPossibleTargetValues(\stdClass $sourceValue, Collection $targetValues, string $whereColumn, ?string $additionalWhereColumn): ?Collection
    {
        // its a possible target, there is a chance the target has no input.
        $possibleTargetValues = $targetValues->where($whereColumn, $sourceValue->{$whereColumn});

        // we may need to narrow down more.
        if ($additionalWhereColumn !== null) {
            $possibleTargetValues = $possibleTargetValues->where($additionalWhereColumn, $sourceValue->{$additionalWhereColumn});
        }

        Log::debug("SOURCE VALUE");
        Log::debug(json_encode($sourceValue));
        Log::debug("TARGET VALUES");
        Log::debug(json_encode($possibleTargetValues->toArray()));

        return $possibleTargetValues;
    }

    /**
     * Complicates copy because it updates data based on target and source data.
     *
     * @param Building $building
     * @param InputSource $from
     * @param InputSource $to
     */
    public static function complicatedCopy(Building $building, InputSource $from, InputSource $to)
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


            if (Schema::hasColumn($table, 'user_id')) {
                $buildingOrUserId = $building->user()->first()->id;
                $buildingOrUserColumn = 'user_id';
            } else {
                $buildingOrUserId = $building->id;
                $buildingOrUserColumn = 'building_id';
            }
            // now we get all the answers from the desired input source

            $fromValues = DB::table($table)
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
                        $toValueQuery = DB::table($table)
                            ->where('input_source_id', $to->id)
                            ->where($buildingOrUserColumn, $buildingOrUserId)
                            ->where($whereColumn, $fromValue->$whereColumn);

                        $toValue = $toValueQuery->first();

                        static::updateOrInsert($to, $fromValue, $toValue, $toValueQuery, $buildingOrUserColumn, $buildingOrUserId);
                    }
                }
            } else {
                // get the resident his input
                $toValueQuery = DB::table($table)
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
        $fromData['updated_at'] = Carbon::now()->toDateTimeString();
        $fromData['input_source_id'] = $to->id;

        if (array_key_exists('extra', $fromData)) {

            $extra = json_decode($fromData['extra'], true);
            if (!is_null($extra)) {
                $fromData['extra'] = static::filterExtraColumn($extra);
            }
            // some extra column contain "null", we dont want that
            if ($fromData['extra'] == "null") {
                $fromData['extra'] = null;
            }
            if (!empty($fromData['extra'])) {
                $fromData['extra'] = json_encode($fromData['extra']);
            }

            if (empty($fromData['extra'])) {
                $fromData['extra'] = null;
            }
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
            DB::table($toValueQuery->from)->insert($fromValue);
        }
    }

    /**
     * Method to copy data from a building and input source to a other input source on the same building.
     */
    public static function copy(Building $building, InputSource $from, InputSource $to)
    {
        $userId = Auth::id();

        Log::debug("BUILDING_ID FOR COPY: {$building->id}");
        Log::debug("AUTH_USER_ID WHICH IS COPYING: {$userId}");

        static::complicatedCopy($building, $from, $to);
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
