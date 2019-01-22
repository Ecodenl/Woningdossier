<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\InputSource;
use App\Services\ToolSettingService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function copy(Request $request)
    {
        $desiredInputSourceName = $request->get('input_source');

        $tablesWithBuildingIds = [
            'building_pv_panels',
            'building_roof_types',
            'building_heaters',
            'building_features',
            'building_paintwork_statuses',
            'user_progresses',
            'building_elements',
            'building_insulated_glazings',
            'building_services',
            'building_appliances',
            'building_user_usages',
            'devices',
        ];

        $tablesWithUserId = [
            'user_action_plan_advices',
            'user_energy_habits',
            'user_interests',
        ];

        // input sources
        $desiredInputSource = InputSource::findByShort($desiredInputSourceName);
        $residentInputSource = InputSource::findByShort('resident');

        // handle the copy for the tables with a building id.
        foreach ($tablesWithBuildingIds as $tableWithBuildingId) {
            // first delete all the resident his input, we dont need it anyway
            \DB::table($tableWithBuildingId)->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $residentInputSource->id)->delete();

            // get the coach input values
            $desiredInputSourceValues = \DB::table($tableWithBuildingId)->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $desiredInputSource->id)->get();

            // change the input source to the resident
            $desiredInputSourceValues->map(function ($desiredInputSourceValue) use ($residentInputSource) {
                $desiredInputSourceValue->input_source_id = $residentInputSource->id;

                return $desiredInputSourceValue;
            });

            foreach ($desiredInputSourceValues as $desiredInputSourceValue) {
                $desiredInputSourceValue = (array) $desiredInputSourceValue;
                unset($desiredInputSourceValue['id']);

                \DB::table($tableWithBuildingId)->insert($desiredInputSourceValue);
            }
        }

        // handle the copy for the tables with a user id instead of a building id
        foreach ($tablesWithUserId as $tableWithUserId) {
            // first delete all the resident his input, we dont need it anyway
            \DB::table($tableWithUserId)->where('user_id', \Auth::id())
                ->where('input_source_id', $residentInputSource->id)->delete();

            // get the coach input values
            $desiredInputSourceValues = \DB::table($tableWithUserId)->where('user_id', \Auth::id())
                ->where('input_source_id', $desiredInputSource->id)->get();

            // change the input source to the resident
            $desiredInputSourceValues->map(function ($desiredInputSourceValue) use ($residentInputSource) {
                $desiredInputSourceValue->input_source_id = $residentInputSource->id;

                return $desiredInputSourceValue;
            });

            foreach ($desiredInputSourceValues as $desiredInputSourceValue) {
                $desiredInputSourceValue = (array) $desiredInputSourceValue;
                unset($desiredInputSourceValue['id']);

                \DB::table($tableWithUserId)->insert($desiredInputSourceValue);
            }
        }

        ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $desiredInputSource->id, false);
        HoomdossierSession::stopUserComparingInputSources();

        return redirect()->back();
    }
}
