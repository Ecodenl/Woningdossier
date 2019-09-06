<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Log;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\Status;
use App\Models\User;
use App\Services\PrivateMessageViewService;
use Carbon\Carbon;
use Illuminate\Auth\AuthManager;
use Spatie\Permission\Models\Role;

class BuildingController extends Controller
{
    /**
     * Handles the data for the show user for a coach, coordinator and cooperation-admin
     *
     * @param  Cooperation  $cooperation
     * @param $buildingId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Cooperation $cooperation, $buildingId)
    {
        // retrieve the user from the building within the current cooperation;
        $user = $cooperation->users()->whereHas('building', function ($query) use ($buildingId) {
            $query->where('id', $buildingId);
        })->first();


        // easier and clean then writing policies.
        $userIsShowingHimself = $user->id == Hoomdossier::user()->id;

        if (!$user instanceof User) {
            \Illuminate\Support\Facades\Log::debug('A admin tried to show a building that does not seem to exists with id: '.$buildingId);
            return redirect(route('cooperation.admin.index'));
        }

        $building = $user->building;
        $this->authorize('show', [$building, $cooperation]);

        $buildingId       = $building->id;

        $roles = Role::where('name', '!=', 'superuser')
                     ->where('name', '!=', 'super-admin')
                     ->where('name', '!=', 'cooperation-admin')
                     ->get();

        $coaches = $cooperation->getCoaches()->get();

        $statuses = Status::ordered()->get();

        $coachesWithActiveBuildingCoachStatus = BuildingCoachStatus::getConnectedCoachesByBuildingId($buildingId);


        $mostRecentStatus = $building->getMostRecentBuildingStatus();

        $logs = Log::forBuildingId($buildingId)->get();

        $privateMessages = PrivateMessage::private()->conversation($buildingId)->get();
        $publicMessages  = PrivateMessage::public()->conversation($buildingId)->get();

        // get all the building notes
        $buildingNotes = $building->buildingNotes()->orderByDesc('updated_at')->get();

        if (Hoomdossier::user()->hasRoleAndIsCurrentRole('coach')) {

        $connectedBuildingsForUser = BuildingCoachStatus::getConnectedBuildingsByUser(Hoomdossier::user(), $cooperation);

        $previous = $connectedBuildingsForUser->where('building_id', '<', $buildingId)->max('building_id');
        $next     = $connectedBuildingsForUser->where('building_id', '>', $buildingId)->min('building_id');

        } else {
            // get previous user id
            $previous = $cooperation
                ->users()
                ->join('buildings', 'users.id', '=', 'buildings.user_id')
                ->where('buildings.id', '<', $buildingId)
                ->max('buildings.id');

        // get next user id
        $next = $cooperation
            ->users()
            ->join('buildings', 'users.id', '=', 'buildings.user_id')
            ->where('buildings.id', '>', $buildingId)
            ->min('buildings.id');
        }



        return view('cooperation.admin.buildings.show', compact(
                'user', 'building', 'roles', 'coaches', 'userIsShowingHimself',
                'coachesWithActiveBuildingCoachStatus', 'mostRecentStatus', 'privateMessages',
                'publicMessages', 'buildingNotes', 'previous', 'next', 'statuses', 'logs'
            )
        );
    }
}
