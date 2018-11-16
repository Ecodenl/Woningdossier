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
        $residentInputSource = InputSource::findByShort('resident');

        // loop trough all the buildings
        foreach ($tables as $table) {

            // first delete all the input from the resident
            // there is no way to update it.
            \DB::table($table)
                ->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $residentInputSource->id)
                ->delete();

            // get all the coach input values.
            $coachInputValues = \DB::table($table)
                ->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $coachInputSource->id)->get();

            // check if there are coach input values.
            // if so, insert the coach input values.
            if ($coachInputValues->isNotEmpty()) {

                foreach ($coachInputValues as $coachInput) {
                    // cast to array
                    // remove the keys we do not want to update
                    $coachInput = (array) $coachInput;
                    unset($coachInput['id'], $coachInput['created_at'], $coachInput['updated_at'], $coachInput['input_source_id']);

                    \DB::table($table)->insert($coachInput);
                }
            }
        }


        /*
         * Copy it for the user energy habits, the table can't be added to the $table array
         * the user_energy_habits does not have a building_id.
         */
        // first delete all the input from the resident
        \DB::table('user_energy_habits')
            ->where('user_id', \Auth::id())
            ->where('input_source_id', $residentInputSource->id)
            ->delete();

        $coachInputForUserEnergyHabit = \DB::table('user_energy_habits')
            ->where('user_id', \Auth::id())
            ->where('input_source_id', $coachInputSource->id)->first();

        // cast to array
        // remove the keys we do not want to update
        $coachInputForUserEnergyHabit = (array) $coachInputForUserEnergyHabit;
        unset($coachInputForUserEnergyHabit['id'], $coachInputForUserEnergyHabit['created_at'], $coachInputForUserEnergyHabit['updated_at'], $coachInputForUserEnergyHabit['input_source_id']);

        \DB::table('user_energy_habits')->insert($coachInputForUserEnergyHabit);

        Log::info('A user imported all answers from a coach');

        return redirect()->route('cooperation.tool.general-data.index');
    }

    /**
     *
     * TODO: Works, but route is turned of and coach user energy habits need to be added. Not a requested feature yet.
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
