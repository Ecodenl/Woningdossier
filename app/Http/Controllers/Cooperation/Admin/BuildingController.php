<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\BuildingFormRequest;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Log;
use App\Models\PrivateMessage;
use App\Models\Scan;
use App\Models\Status;
use App\Models\User;
use App\Services\BuildingAddressService;
use App\Services\BuildingCoachStatusService;
use App\Services\UserRoleService;
use App\Models\Role;

class BuildingController extends Controller
{
    /**
     * Handles the data for the show user for a coach, coordinator and cooperation-admin.
     *
     * @param $buildingId
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show(UserRoleService $userRoleService, Cooperation $cooperation, $buildingId)
    {
        // retrieve the user from the building within the current cooperation;
        $user = $cooperation->users()->whereHas('building', function ($query) use ($buildingId) {
            $query->where('id', $buildingId);
        })->first();

        if (! $user instanceof User) {
            \Illuminate\Support\Facades\Log::debug('An admin tried to show a building that does not seem to exists with id: '.$buildingId);

            return redirect(route('cooperation.admin.index'));
        }

        $building = $user->building;

        $this->authorize('show', [$building]);

        $buildingId = $building->id;

        $roles = Role::all();

        $coaches = $cooperation->getCoaches()->get();

        $statuses = Status::ordered()->get();

        $coachesWithActiveBuildingCoachStatus = BuildingCoachStatusService::getConnectedCoachesByBuildingId($buildingId);

        $mostRecentStatus = $building->getMostRecentBuildingStatus();

        $logs = Log::forBuildingId($buildingId)->get();

        $privateMessages = PrivateMessage::private()->conversation($buildingId)->get();
        $publicMessages = PrivateMessage::public()->conversation($buildingId)->get();

        // get all the building notes
        $buildingNotes = $building->buildingNotes()->orderByDesc('updated_at')->get();

        $scan = $cooperation->scans()->where('short', '!=', Scan::EXPERT)->first();
        $scans = $cooperation->load(['scans' => fn($q) => $q->where('short', '!=', Scan::EXPERT)])->scans;

        return view('cooperation.admin.buildings.show', compact(
                'user', 'building', 'roles', 'coaches', 'scans',
                'coachesWithActiveBuildingCoachStatus', 'mostRecentStatus', 'privateMessages',
                'publicMessages', 'buildingNotes', 'statuses', 'logs', 'scan',
            )
        );
    }

    public function edit(Cooperation $cooperation, Building $building)
    {
        $user = $building->user()->with('account')->first();
        $account = $user->account;

        return view('cooperation.admin.buildings.edit', compact('building', 'user', 'account'));
    }

    public function update(BuildingFormRequest $request, BuildingAddressService $buildingAddressService, Cooperation $cooperation, Building $building)
    {
        $validatedData = $request->validated();
        if (! is_null($validatedData['users']['extra']['contact_id'] ?? null)) {
            // Force as INT
            $validatedData['users']['extra']['contact_id'] = (int) $validatedData['users']['extra']['contact_id'];
        }

        $buildingAddressService->forBuilding($building)->updateAddress($validatedData['buildings']);

        $buildingAddressService->forBuilding($building)->attachMunicipality();

        $validatedData['users']['phone_number'] = $validatedData['users']['phone_number'] ?? '';
        $building->user->update($validatedData['users']);
        $building->user->account->update($validatedData['accounts']);

        return redirect()->route('cooperation.admin.buildings.edit', compact('building'))->with('success', __('cooperation/admin/buildings.update.success'));
    }
}
