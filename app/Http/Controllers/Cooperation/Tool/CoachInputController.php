<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use \Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CoachInputController extends Controller
{

    /**
     * Copy the coach input to the resident from tables that have a building, input source id and a additional where column to query on
     *
     * @param array $tablesWithAdditionalWhereColumn
     */
    protected function copyCoachInputWithBuildingAndInputSourceWithAdditionalWhereColumn(array $tablesWithAdditionalWhereColumn)
    {
        // input sources
        $coachInputSource = InputSource::findByShort('coach');
        $residentInputSource = InputSource::findByShort('resident');

        // loop through the additional tables with extra where column
        foreach ($tablesWithAdditionalWhereColumn as $table => $additionalWhere) {
            // update the coach input
            $coachInputSourceValues = \DB::table($table)
                ->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $coachInputSource->id)
                ->get();

            // check if there are answers from the coach
            if ($coachInputSourceValues->isNotEmpty()) {

                foreach ($coachInputSourceValues as $coachInputSourceValue) {
                    // cast to array
                    // remove the keys we do not want to update
                    $coachInputSourceValue = (array) $coachInputSourceValue;
                    unset($coachInputSourceValue['id'], $coachInputSourceValue['created_at'], $coachInputSourceValue['updated_at'], $coachInputSourceValue['input_source_id']);

                    // update the resident records or create a new record for the resident
                    \DB::table($table)->updateOrInsert(
                        [
                            'building_id' => HoomdossierSession::getBuilding(),
                            'input_source_id' => $residentInputSource->id,
                            $additionalWhere => $coachInputSourceValue[$additionalWhere]
                        ],
                        $coachInputSourceValue
                    );
                }
            }
        }
    }

    /**
     * Copy the input of the coach to the resident from tables that have a building and input source id
     *
     * @param array $tablesWithBuildingAndInputSourceId
     */
    protected function copyCoachInputWithBuildingAndInputSource(array $tablesWithBuildingAndInputSourceId)
    {
        // input sources
        $coachInputSource = InputSource::findByShort('coach');
        $residentInputSource = InputSource::findByShort('resident');

        // loop trough all the buildings
        foreach ($tablesWithBuildingAndInputSourceId as $tableWithBuildingAndInputSourceId) {

            // get all the coach input values.
            $coachInputValues = \DB::table($tableWithBuildingAndInputSourceId)
                ->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $coachInputSource->id)->get();

            // check if there are answers from the coach
            if ($coachInputValues->isNotEmpty()) {

                foreach ($coachInputValues as $coachInput) {
                    // cast to array
                    // remove the keys we do not want to update
                    $coachInput = (array)$coachInput;
                    unset($coachInput['id'], $coachInput['created_at'], $coachInput['updated_at'], $coachInput['input_source_id']);

                    // update the resident records or create a new record for the resident
                    \DB::table($tableWithBuildingAndInputSourceId)->updateOrInsert(
                        [
                            'building_id' => HoomdossierSession::getBuilding(),
                            'input_source_id' => $residentInputSource->id,
                        ],
                        $coachInput
                    );
                }
            }
        }
    }

    /**
     * Copy the coach input to the resident from tables that have a user and input source id
     *
     * @param array $tablesWithUserIdAndInputSourceId
     */
    protected function copyCoachInputWithUserAndInputSource(array $tablesWithUserIdAndInputSourceId)
    {
        // input sources
        $coachInputSource = InputSource::findByShort('coach');
        $residentInputSource = InputSource::findByShort('resident');

        foreach ($tablesWithUserIdAndInputSourceId as $tableWithUserIdAndInputSourceId) {

            $coachInputForUserEnergyHabit = \DB::table($tableWithUserIdAndInputSourceId)
                ->where('user_id', \Auth::id())
                ->where('input_source_id', $coachInputSource->id)->first();

            // cast to array
            // remove the keys we do not want to update
            $coachInputForUserEnergyHabit = (array) $coachInputForUserEnergyHabit;
            unset($coachInputForUserEnergyHabit['id'], $coachInputForUserEnergyHabit['created_at'], $coachInputForUserEnergyHabit['updated_at'], $coachInputForUserEnergyHabit['input_source_id']);

            \DB::table($tableWithUserIdAndInputSourceId)->updateOrInsert(
                [
                    'user_id' => \Auth::id(),
                    'input_source_id' => $residentInputSource->id,
                ],
                $coachInputForUserEnergyHabit
            );
        }
    }

    /**
     * Copy the coach input to the resident from tables that have a user and input source id and additional columns to query on
     *
     * @param array $tablesWithUserIdAndInputSourceIdWithAdditionalWhere
     */
    protected function copyCoachInputWithUserAndInputSourceWithAdditionalWhereColumns(array $tablesWithUserIdAndInputSourceIdWithAdditionalWhere)
    {
        // input sources
        $coachInputSource = InputSource::findByShort('coach');
        $residentInputSource = InputSource::findByShort('resident');

        foreach ($tablesWithUserIdAndInputSourceIdWithAdditionalWhere as $tableWithUserIdAndInputSourceIdWithAdditionalWhere => $additionalWheres) {

            // $where to check on in the updateOrInsert
            $where = [];

            // the coach input
            $coachInputSourceValues = \DB::table($tableWithUserIdAndInputSourceIdWithAdditionalWhere)
                ->where('user_id', \Auth::id())
                ->where('input_source_id', $coachInputSource->id)->get();

            // check if there are answers from the coach
            if ($coachInputSourceValues->isNotEmpty()) {

                // loop through the answers
                foreach ($coachInputSourceValues as $coachInputSourceValue) {

                    // cast to array
                    // remove the keys we do not want to update
                    $coachInputSourceValue = (array) $coachInputSourceValue;
                    unset($coachInputSourceValue['id'], $coachInputSourceValue['created_at'], $coachInputSourceValue['updated_at'], $coachInputSourceValue['input_source_id']);

                    // add the wheres that should always be added
                    $where['user_id'] = \Auth::id();
                    $where['input_source_id'] = $residentInputSource->id;

                    // add the additional columns to the where array with matching values so we can put the $where array in the update or insert.
                    foreach ($additionalWheres as $additionalWhereColumn) {
                        $where[$additionalWhereColumn] = $coachInputSourceValue[$additionalWhereColumn];
                    }
                    Log::debug($where);


                    // update or insert the values
                    \DB::table($tableWithUserIdAndInputSourceIdWithAdditionalWhere)->updateOrInsert(
                        $where,
                        $coachInputSourceValue
                    );

                }
            }
        }

    }
    /**
     * Copy the coach input to the resident
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copy()
    {
        // all the tables that have a building_id and input_source_id
        $tablesWithBuildingAndInputSourceId = [
            'building_user_usages',
            'building_appliances',
            'building_pv_panels',
            'building_paintwork_statuses',
            'devices',
            'building_roof_types',
            'building_heaters',
            'building_features',
        ];
        $this->copyCoachInputWithBuildingAndInputSource($tablesWithBuildingAndInputSourceId);

        // all the tables that have a building_id, input_source_id and additional column to query on
        $tablesWithAdditionalWhereColumn = [
            'building_elements' => 'element_id',
            'building_insulated_glazings' => 'measure_application_id',
            'building_services' => 'service_id',
        ];
        $this->copyCoachInputWithBuildingAndInputSourceWithAdditionalWhereColumn($tablesWithAdditionalWhereColumn);

        // all the tables that have a user and building id without a additional where column
        $tablesWithUserIdAndInputSourceId = [
            'user_energy_habits',
        ];
        $this->copyCoachInputWithUserAndInputSource($tablesWithUserIdAndInputSourceId);


        // tables that have a user and input source id with additional where column / columns
        $tablesWithUserIdAndInputSourceIdWithAdditionalWhere = [
            'user_interests' => [
                'interested_in_type',
                'interested_in_id',
            ]
        ];
        $this->copyCoachInputWithUserAndInputSourceWithAdditionalWhereColumns($tablesWithUserIdAndInputSourceIdWithAdditionalWhere);


        Log::info('A user imported all answers from a coach');

        return redirect()->route('cooperation.tool.general-data.index');
    }

    /**
     *
     * TODO: Works, but route is turned of. Not a requested feature yet.
     * Remove the coach input for a resident
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $tables = [
            'building_user_usages',
            'building_elements',
            'building_insulated_glazings',
            'building_services',
            'building_appliances',
            'building_pv_panels',
            'building_paintwork_statuses',
            'devices',
            'building_roof_types',
            'building_heaters',
            'building_features',
        ];

        $coachInputSource = InputSource::findByShort('coach');

        foreach ($tables as $table) {
            \DB::table($table)
                ->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $coachInputSource->id)->delete();
        }

        // and delete the user energy habits
        \DB::table('user_energy_habits')
            ->where('user_id', \Auth::id())
            ->where('input_source_id', $coachInputSource->id)->delete();

        return redirect()->route('cooperation.tool.general-data.index');

    }
}
