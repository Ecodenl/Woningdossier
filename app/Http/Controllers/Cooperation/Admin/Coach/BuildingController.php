<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use Carbon\Carbon;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class BuildingController extends Controller
{
    public function index()
    {
        $buildingPermissions = \Auth::user()->buildingPermissions;
        $buildingCoachStatuses = BuildingCoachStatus::all();

        return view('cooperation.admin.coach.buildings.index', compact('buildingPermissions', 'buildingCoachStatuses'));
    }

    public function edit(Cooperation $cooperation, $buildingId)
    {
        $building = Building::find($buildingId);
        // do a check if the user has access to this building
        if (\Auth::user()->buildingPermissions()->where('building_id', $buildingId)->first() instanceof BuildingPermission) {
            $buildingCoachStatus = BuildingCoachStatus::where('building_id', $buildingId)->first();
        }

        return view('cooperation.admin.coach.buildings.edit', compact('building', 'buildingCoachStatus'));
    }


    public function update(Request $request)
    {

        $buildingCoachStatus = $request->get('building_coach_status', '');
        $appointmentDate = $request->get('appointment_date', null);
        $appointmentDateFormated = Carbon::parse($appointmentDate)->format('Y-m-d H:i:s');
        $buildingId = $request->get('building_id');

        BuildingCoachStatus::updateOrCreate(
            [
                'coach_id' => \Auth::id(),
                'building_id' => $buildingId
            ],
            [
                'appointment_date' => $appointmentDateFormated,
                'status' => $buildingCoachStatus,
            ]
        );

        return redirect()->route('cooperation.admin.coach.buildings.index')->with('success', __('woningdossier.cooperation.admin.coach.buildings.set-building-status.success'));


    }

    /**
     * Set the sessions and after that redirect them to the tool
     *
     * @param Cooperation $cooperation
     * @param $buildingId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fillForUser(Cooperation $cooperation, $buildingId)
    {
        // The building the coach wants to edit
        $building = Building::find($buildingId);
        // get the owner of the building
        $user = User::find($building->user_id);;
        // we cant query on the Spatie\Role model so we first get the result on the "original model"
        $role = Role::findByName($user->roles->first()->name);
        // get the input source
        $inputSourceValue = $role->inputSource;

        $inputSource = InputSource::find(HoomdossierSession::getInputSource());

        // if the role has no inputsource redirect back with "probeer t later ff nog een keer"
        // note: this will not occur much
        if (!$inputSourceValue instanceof InputSource || !$inputSource instanceof InputSource) {
            return redirect()->back()->with('warning', __('woningdossier.cooperation.admin.coach.buildings.fill-for-user.warning'));
        }

        // We set the building to the building the coach wants to "edit"
        // The inputSource is just the coach one
        // But the input source value is from the building owner so the coach can see the input, the coach can switch this in the tool itself.
        HoomdossierSession::setHoomdossierSessions($building, $inputSource, $inputSourceValue, $role);

        return redirect()->route('cooperation.tool.index');
    }
}
