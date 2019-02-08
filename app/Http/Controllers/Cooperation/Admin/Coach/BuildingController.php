<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingNotes;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class BuildingController extends Controller
{
    public function index()
    {
        // @note, no need to do a withThrashed.
//
//        // get the buildings from the notes
//        $buildingsFromNotes = \DB::table('building_notes')
//            ->where('building_notes.coach_id', '=', \Auth::id())
//            ->leftJoin('buildings', 'buildings.id', '=', 'building_notes.building_id')
//            ->leftJoin('users', 'users.id', '=', 'buildings.user_id')
//            ->select('buildings.*', 'users.first_name', 'users.last_name')
//            ->distinct()
//            ->get();
//
        ////        $buildingsFromNotes = BuildingNotes::where('coach_id', \Auth::id())->with(['building' => function ($query) {
        ////            $query->withTrashed();
        ////        }])->get();
//
//        // get the buildings from the buildings permissions
//        $buildingsFromPermissions = \DB::table('building_permissions')
//            ->where('building_permissions.user_id', '=', \Auth::id())
//            ->leftJoin('buildings', 'buildings.id', '=', 'building_permissions.building_id')
//            ->leftJoin('users', 'users.id', '=', 'buildings.user_id')
//            ->select('buildings.*', 'users.first_name', 'users.last_name')
//            ->distinct()
//            ->get();
//
//        // merge the results and make them unique
//        $buildings = $buildingsFromNotes->merge($buildingsFromPermissions)->unique();

        $buildingsFromCoachStatuses = \DB::table('building_coach_statuses')
            ->where('building_coach_statuses.coach_id', '=', \Auth::id())
            ->leftJoin('buildings', 'buildings.id', '=', 'building_coach_statuses.building_id')
            ->leftJoin('users', 'users.id', '=', 'buildings.user_id')
            ->leftJoin('private_messages', 'private_messages.id', '=', 'building_coach_statuses.private_message_id')
            ->select('buildings.*', 'users.first_name', 'users.last_name', 'private_messages.allow_access')
            ->get()->unique('id');

        $buildingCoachStatuses = BuildingCoachStatus::all();

        return view('cooperation.admin.coach.buildings.index', compact('buildings', 'buildingCoachStatuses', 'buildingsFromCoachStatuses'));
    }

    public function edit(Cooperation $cooperation, $buildingId)
    {
        $building = Building::withTrashed()->find($buildingId);
        // do a check if the user has access to this building
        if (\Auth::user()->buildingPermissions()->where('building_id', $buildingId)->first() instanceof BuildingPermission) {
            $buildingCoachStatus = BuildingCoachStatus::where('building_id', $buildingId)->first();
        }

        $buildingCoachStatuses = BuildingCoachStatus::where('coach_id', \Auth::id())->where('building_id', $buildingId)->get();

        return view('cooperation.admin.coach.buildings.edit', compact('building', 'buildingCoachStatus', 'buildingCoachStatuses'));
    }

    public function update(Request $request)
    {
        $buildingCoachStatus = $request->get('building_coach_status', '');
        $appointmentDate = $request->get('appointment_date', null);
        $privateMessageId = $request->get('private_message_id');

        $appointmentDateFormated = null;
        if (! empty($appointmentDate)) {
            $appointmentDateFormated = Carbon::parse($appointmentDate)->format('Y-m-d H:i:s');
        }
        $buildingId = $request->get('building_id');

        BuildingCoachStatus::create(
            [
                'coach_id' => \Auth::id(),
                'building_id' => $buildingId,
                'appointment_date' => $appointmentDateFormated,
                'status' => $buildingCoachStatus,
                'private_message_id' => $privateMessageId,
            ]
        );

        return redirect()->route('cooperation.admin.coach.buildings.index')->with('success', __('woningdossier.cooperation.admin.coach.buildings.set-building-status.success'));
    }

    /**
     * Set the sessions and after that redirect them to the tool.
     *
     * @param Cooperation $cooperation
     * @param $buildingId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fillForUser(Cooperation $cooperation, $buildingId)
    {
        // The building the coach wants to edit
        $building = Building::find($buildingId);
        // get the owner of the building
        $user = User::find($building->user_id);
        // we cant query on the Spatie\Role model so we first get the result on the "original model"
        $role = Role::findByName($user->roles->first()->name);
        // set the input source value to the coach itself
        $inputSourceValue = InputSource::find(HoomdossierSession::getInputSource());

        $inputSource = InputSource::find(HoomdossierSession::getInputSource());

        // if the role has no inputsource redirect back with "probeer t later ff nog een keer"
        // or if the role is not a resident, we gonna throw them back.
        if (! $inputSourceValue instanceof InputSource || ! $inputSource instanceof InputSource && $inputSource->isResident()) {
            return redirect()->back()->with('warning', __('woningdossier.cooperation.admin.coach.buildings.fill-for-user.warning'));
        }

        // We set the building to the building the coach wants to "edit"
        // The inputSource is just the coach one
        // But the input source value is from the building owner so the coach can see the input, the coach can switch this in the tool itself.
        HoomdossierSession::setHoomdossierSessions($building, $inputSource, $inputSourceValue, $role);

        return redirect()->route('cooperation.tool.index');
    }
}
