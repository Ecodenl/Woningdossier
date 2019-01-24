<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Services\ToolSettingService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function copy(Request $request)
    {
        $desiredInputSourceName = $request->get('input_source');

        $tablesWithBuildingIds = [
//            'questions_answers',
//            'building_pv_panels',
            'building_roof_types',
//            'building_heaters',
            'building_features',
//            'building_paintwork_statuses',
//            'user_progresses',
//            'building_elements',
//            'building_insulated_glazings',
//            'building_services',
//            'building_appliances',
//            'building_user_usages',
//            'devices',
        ];

        $tablesWithUserId = [
            'user_action_plan_advices',
            'user_energy_habits',
            'user_interests',
        ];

        // input sources
        $desiredInputSource = InputSource::findByShort($desiredInputSourceName);
        $residentInputSource = InputSource::findByShort('resident');

        $exampleBuilding  = ExampleBuildingContent::find(89);

        foreach ($exampleBuilding->content as $stepSlug => $contents) {
            foreach ($contents as $table => $content) {

                // can be user or building_id
                $userOrBuildingIdWhere = 'user_id';
                $userOrBuildingId = \Auth::id();
                // check if the table has a building id column
                if (\Schema::hasColumn($table, 'building_id')) {
                    $userOrBuildingIdWhere = 'building_id';
                    $userOrBuildingId = HoomdossierSession::getBuilding();
                }

                if (!\Schema::hasTable($table)) {
                    dump($table);
                    dump($contents);
                }
                if ($table == "building_roof_types") {
//                    dd($contents);


                }
//                // query on the base information.
//                $baseResidentInputSourceQuery = \DB::table($table)
//                    ->where('input_source_id', $residentInputSource->id)
//                    ->where($userOrBuildingIdWhere, $userOrBuildingId)->get();


                $additionalWhereColumn = '';
                switch ($table) {
                    case 'building_roof_types':
                        $additionalWhereColumn = 'roof_type_id';
                }

            }
        }
        dd();

        // handle the copy for the tables with a building id.
        foreach ($tablesWithBuildingIds as $tableWithBuildingId) {
            // first delete all the resident his input, we dont need it anyway
            $residentInput = \DB::table($tableWithBuildingId)->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $residentInputSource->id)->get();

            // get the coach input values
            $desiredInputSourceValues = \DB::table($tableWithBuildingId)->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $desiredInputSource->id)->get();
            dd($residentInput->merge($desiredInputSourceValues));


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
