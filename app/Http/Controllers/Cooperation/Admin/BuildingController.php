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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Cooperation $cooperation, $buildingId)
    {
        // retrieve the user from the building within the current cooperation;
        $user = $cooperation->users()->whereHas('building', function ($query) use ($buildingId) {
            $query->where('id', $buildingId);
        })->first();


        if (!$user instanceof User) {
            \Illuminate\Support\Facades\Log::debug('A admin tried to show a building that does not seem to exists with id: '.$buildingId);
            return redirect(route('cooperation.admin.index'));
        }

        $building = $user->building;
        $this->authorize('show', [$building, $cooperation]);


        $userDoesNotExist = ! $user instanceof User;
        $userExists       = ! $userDoesNotExist;
        $buildingId       = $building->id;

        $roles = Role::where('name', '!=', 'superuser')
                     ->where('name', '!=', 'super-admin')
                     ->where('name', '!=', 'cooperation-admin')
                     ->get();

        $coaches = $cooperation->getCoaches()->get();

        $manageableStatuses                   = BuildingCoachStatus::getManageableStatuses();
        $coachesWithActiveBuildingCoachStatus = BuildingCoachStatus::getConnectedCoachesByBuildingId($buildingId);

        $mostRecentStatusesForBuildingId = BuildingCoachStatus::getMostRecentStatusesForBuildingId($buildingId);

        $mostRecentBcs = [];
        // first check if there are any.
        if ($mostRecentStatusesForBuildingId->isNotEmpty()) {
            // if the user is a coach we can get the specific one for the current coach
            // else we just get the most recent one.
            if (Hoomdossier::user()->hasRoleAndIsCurrentRole('coach')) {
                $mostRecentBcs = $mostRecentStatusesForBuildingId->where('coach_id', Hoomdossier::user()->id)->all();
            } else {
                $mostRecentBuildingCoachStatusArray = $mostRecentStatusesForBuildingId->all();
                $mostRecentBcs                      = [$mostRecentBuildingCoachStatusArray[0]];
            }
        }

        // hydrate the building coach status model so it will be easier to do stuff in the views
        $mostRecentBuildingCoachStatus = BuildingCoachStatus::hydrate(
            $mostRecentBcs
        )->first();

        $logs = Log::forBuildingId($buildingId)->get();

        $privateMessages = PrivateMessage::private()->conversation($buildingId)->get();
        $publicMessages  = PrivateMessage::public()->conversation($buildingId)->get();

        // and set them all to read.
        if (\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            PrivateMessageViewService::markAsReadByCooperation($privateMessages, $cooperation);
            PrivateMessageViewService::markAsReadByCooperation($publicMessages, $cooperation);
        }
        elseif(Hoomdossier::user()->hasRoleAndIsCurrentRole('coach')) {
            $inputSource = InputSource::findByShort(InputSource::COACH_SHORT);
            PrivateMessageViewService::markAsReadByUser($privateMessages, Hoomdossier::user(), $inputSource);
            PrivateMessageViewService::markAsReadByUser($privateMessages, Hoomdossier::user(), $inputSource);
        }

        //PrivateMessageViewService::setRead($privateMessages);
        //PrivateMessageViewService::setRead($publicMessages);

        // get all the building notes
        $buildingNotes = $building->buildingNotes()->orderByDesc('updated_at')->get();

        // since a user can be deleted, a building
        if ($userExists) {
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

        }


        return view('cooperation.admin.buildings.show', compact(
                'user', 'building', 'roles', 'coaches', 'lastKnownBuildingCoachStatus',
                'coachesWithActiveBuildingCoachStatus',
                'privateMessages', 'publicMessages', 'buildingNotes', 'previous', 'next', 'manageableStatuses',
                'mostRecentBuildingCoachStatus',
                'userDoesNotExist', 'userExists', 'logs'
            )
        );
    }
}
