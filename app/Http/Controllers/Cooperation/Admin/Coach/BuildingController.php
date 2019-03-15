<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {
        $userId = \Auth::id();
        // get most recent building coach statuses for
        $buildingCoachStatuses = BuildingCoachStatus::hydrate(
            \DB::table('building_coach_statuses as bcs1')->select('coach_id', 'building_id', 'created_at', 'status', 'appointment_date')
                ->where('created_at', function ($query) use ($userId) {
                    $query->select(\DB::raw('MAX(created_at)'))
                        ->from('building_coach_statuses as bcs2')
                        ->whereRaw('coach_id = ' . $userId . ' and bcs1.building_id = bcs2.building_id');
                })->where('coach_id', $userId)
                ->orderBy('created_at')
                ->get()->all()
        );


        return view('cooperation.admin.coach.buildings.index', compact('buildings', 'buildingCoachStatuses'));
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

    public function show(Cooperation $cooperation, $buildingId)
    {
        $building = Building::withTrashed()->find($buildingId);
        $user = $cooperation->users()->find($building->user_id);
        $userDoesNotExist = !$user instanceof User;
        $buildingId = $building->id;
        $roles = Role::all();
        $coaches = $cooperation->getCoaches()->get();
        $lastKnownBuildingCoachStatus = $building->buildingCoachStatuses->last();

        $manageableStatuses = BuildingCoachStatus::getManageableStatuses();

        $mostRecentStatusesForBuildingId = BuildingCoachStatus::getMostRecentStatusesForBuildingId($buildingId);

        // get the most recent status for the current coach and hydrate it.
        $mostRecentBuildingCoachStatus = BuildingCoachStatus::hydrate(
            $mostRecentStatusesForBuildingId->where('coach_id', \Auth::id())->all()
        )->first();


        $privateMessages = PrivateMessage::forMyCooperation()->private()->conversation($buildingId)->get();
        $publicMessages = PrivateMessage::forMyCooperation()->public()->conversation($buildingId)->get();

        // get all the building notes
        $buildingNotes = $building->buildingNotes()->orderByDesc('updated_at')->get();

        // since a user can be deleted, a buildin
        if ($user instanceof User) {
            // get previous user id
            $previous = $cooperation->users()->where('id', '<', $user->id)->max('id');
            // get next user id
            $next = $cooperation->users()->where('id', '>', $user->id)->min('id');
        }

        return view('cooperation.admin.coach.buildings.show', compact(
                'user', 'building', 'roles', 'coaches', 'lastKnownBuildingCoachStatus', 'coachesWithActiveBuildingCoachStatus',
                'privateMessages', 'publicMessages', 'buildingNotes', 'previous', 'next', 'manageableStatuses', 'mostRecentBuildingCoachStatus',
                'userDoesNotExist'
            )
        );
    }

}
