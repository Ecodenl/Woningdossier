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
     * Copy the coach input to the resident
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copy()
    {
        // all the tables that have a building_id and input_source_id
        $tables = [
            'building_user_usages',
            'building_appliances',
            'building_pv_panels',
            'building_paintwork_statuses',
            'devices',
            'building_roof_types',
            'building_heaters',
            'building_features',
        ];

        // all the tables that have a building_id, input_source_id and additional column to query on
        $tablesWithAdditionalWhereColumn = [
            'building_elements' => 'element_id',
            'building_insulated_glazings' => 'measure_application_id',
            'building_services' => 'service_id',
        ];

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

        // loop trough all the buildings
        foreach ($tables as $table) {

            // get all the coach input values.
            $coachInputValues = \DB::table($table)
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
                    \DB::table($table)->updateOrInsert(
                        [
                            'building_id' => HoomdossierSession::getBuilding(),
                            'input_source_id' => $residentInputSource->id,
                        ],
                        $coachInput
                    );
                }
            }
        }

        /*
         * Copy it for the user energy habits, the table can't be added to the $table array
         * the user_energy_habits does not have a building_id.
         */
        $coachInputForUserEnergyHabit = \DB::table('user_energy_habits')
            ->where('user_id', \Auth::id())
            ->where('input_source_id', $coachInputSource->id)->first();

        // cast to array
        // remove the keys we do not want to update
        $coachInputForUserEnergyHabit = (array) $coachInputForUserEnergyHabit;
        unset($coachInputForUserEnergyHabit['id'], $coachInputForUserEnergyHabit['created_at'], $coachInputForUserEnergyHabit['updated_at'], $coachInputForUserEnergyHabit['input_source_id']);

        \DB::table('user_energy_habits')->updateOrInsert(
            [
                'user_id' => \Auth::id(),
                'input_source_id' => $residentInputSource->id,
            ],
            $coachInputForUserEnergyHabit
        );

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
