<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
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

        foreach ($tables as $table) {

            $coachInput = \DB::table($table)
                ->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $coachInputSource->id)->first();

            // check if there are coach input values.
            // if so, update the resident values to the coach values
            if (!empty($coachInput)) {

                $residentInputQuery = \DB::table($table)
                    ->where('building_id', HoomdossierSession::getBuilding())
                    ->where('input_source_id', $residentInputSource->id);

                // cast to array
                // remove the keys we do not want to update
                $coachInput = (array) $coachInput;
                unset($coachInput['id'], $coachInput['created_at'], $coachInput['updated_at'], $coachInput['input_source_id']);

                // get the first residentInput, and cast it to an array
                $residentInputArray = (array) $residentInputQuery->first();
                // if its not empty, we update it else we insert it.
                if (!empty($residentInputArray)) {
                    $residentInputQuery->update($coachInput);
                } else {
                    $residentInputQuery->insert($coachInput);
                }
            }
        }

        // need to do manually, user_energy_habits does not have a building id.
        $coachUserEnergyHabitsForUser = \DB::table('user_energy_habits')
            ->where('user_id', \Auth::id())
            ->where('input_source_id', $coachInputSource->id)->first();

        // cast to array and unset, same as above.
        $coachUserEnergyHabitsForUser = (array) $coachUserEnergyHabitsForUser;
        unset($coachUserEnergyHabitsForUser['id'], $coachUserEnergyHabitsForUser['created_at'], $coachUserEnergyHabitsForUser['updated_at'], $coachUserEnergyHabitsForUser['input_source_id']);

        if (!empty($coachUserEnergyHabitsForUser)) {

            $residentUserEnergyHabitsQuery = \DB::table('user_energy_habits')
                ->where('user_id', \Auth::id())
                ->where('input_source_id', $residentInputSource->id);

            // get the first residentInput, and cast it to an array
            $residentInputArray = (array) $residentUserEnergyHabitsQuery->first();
            // if its not empty, we update it else we insert it.
            if (!empty($residentInputArray)) {
                $residentUserEnergyHabitsQuery->update($coachUserEnergyHabitsForUser);
            } else {
                $residentUserEnergyHabitsQuery->insert($coachUserEnergyHabitsForUser);
            }

        }


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

        return redirect()->route('cooperation.tool.general-data.index');

    }
}
