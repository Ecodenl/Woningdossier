<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Models\BuildingAppliance;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\BuildingService;
use App\Models\BuildingUserUsage;
use App\Models\Device;
use App\Models\InputSource;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportController extends Controller
{
    public function copy()
    {
        $tablesWithBuildingIds = [
            'building_pv_panels',
            'building_roof_types',
            'building_heaters',
            'building_features',
            'building_paintwork_statuses',
            'building_elements',
            'building_insulated_glazings',
            'building_services',
            'building_appliances',
            'building_user_usages',
            'devices',
        ];
        
        $tablesWithUserId = [
            'user_energy_habits'
        ];

        // input sources
        $coachInputSource = InputSource::findByShort('coach');
        $residentInputSource = InputSource::findByShort('resident');

        // handle the copy for the tables with a building id.
        foreach ($tablesWithBuildingIds as $tableWithBuildingId) {

            // first delete all the resident his input, we dont need it anyway
            \DB::table($tableWithBuildingId)->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $residentInputSource->id)->delete();

            // get the coach input values
            $coachInputSourceValues = \DB::table($tableWithBuildingId)->where('building_id', HoomdossierSession::getBuilding())
                ->where('input_source_id', $coachInputSource->id)->get();

            // change the input source to the resident
            $coachInputSourceValues->map(function ($coachInputSourceValue) use ($residentInputSource) {
                $coachInputSourceValue->input_source_id = $residentInputSource->id;
                return $coachInputSourceValue;
            });

            foreach ($coachInputSourceValues as $coachInputSourceValue) {

                $coachInputSourceValue = (array) $coachInputSourceValue;
                unset($coachInputSourceValue['id']);

                \DB::table($tableWithBuildingId)->insert($coachInputSourceValue);
            }

        }

        // handle the copy for the tables with a user id instead of a building id
        foreach ($tablesWithUserId as $tableWithUserId) {
            // first delete all the resident his input, we dont need it anyway
            \DB::table($tableWithUserId)->where('user_id', \Auth::id())
                ->where('input_source_id', $residentInputSource->id)->delete();

            // get the coach input values
            $coachInputSourceValues = \DB::table($tableWithUserId)->where('user_id', \Auth::id())
                ->where('input_source_id', $coachInputSource->id)->get();

            // change the input source to the resident
            $coachInputSourceValues->map(function ($coachInputSourceValue) use ($residentInputSource) {
                $coachInputSourceValue->input_source_id = $residentInputSource->id;
                return $coachInputSourceValue;
            });

            foreach ($coachInputSourceValues as $coachInputSourceValue) {

                $coachInputSourceValue = (array) $coachInputSourceValue;
                unset($coachInputSourceValue['id']);

                \DB::table($tableWithUserId)->insert($coachInputSourceValue);
            }
        }

    }
}
