<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\BuildingFormRequest;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Log;
use App\Models\PrivateMessage;
use App\Models\Status;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use Spatie\Permission\Models\Role;

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
    public function show(Cooperation $cooperation, $buildingId)
    {
        // retrieve the user from the building within the current cooperation;
        $user = $cooperation->users()->whereHas('building', function ($query) use ($buildingId) {
            $query->where('id', $buildingId);
        })->first();

        if (! $user instanceof User) {
            \Illuminate\Support\Facades\Log::debug('A admin tried to show a building that does not seem to exists with id: '.$buildingId);

            return redirect(route('cooperation.admin.index'));
        }

        $building = $user->building;
        $this->authorize('show', [$building]);

        $buildingId = $building->id;

        $roles = Role::where('name', '!=', 'superuser')
            ->where('name', '!=', 'super-admin')
            ->where('name', '!=', 'cooperation-admin')
            ->get();

        $coaches = $cooperation->getCoaches()->get();

        $statuses = Status::ordered()->get();

        $coachesWithActiveBuildingCoachStatus = BuildingCoachStatusService::getConnectedCoachesByBuildingId($buildingId);

        $mostRecentStatus = $building->getMostRecentBuildingStatus();

        $logs = Log::forBuildingId($buildingId)->get();

        $privateMessages = PrivateMessage::private()->conversation($buildingId)->get();
        $publicMessages = PrivateMessage::public()->conversation($buildingId)->get();

        // get all the building notes
        $buildingNotes = $building->buildingNotes()->orderByDesc('updated_at')->get();

        return view('cooperation.admin.buildings.show', compact(
                'user', 'building', 'roles', 'coaches',
                'coachesWithActiveBuildingCoachStatus', 'mostRecentStatus', 'privateMessages',
                'publicMessages', 'buildingNotes', 'statuses', 'logs'
            )
        );
    }

    public function edit(Cooperation $cooperation, Building $building)
    {
        $user = $building->user()->with('account')->first();
        $account = $user->account;

        return view('cooperation.admin.buildings.edit', compact('building', 'user', 'account'));
    }

    public function update(BuildingFormRequest $request, Cooperation $cooperation, Building $building)
    {
        $validatedData = $request->validated();

        // cant be null in the table.
        $validatedData['buildings']['extension'] = $validatedData['buildings']['extension'] ?? '';
        $validatedData['users']['phone_number'] = $validatedData['users']['phone_number'] ?? '';

        $building->update($validatedData['buildings']);
        $building->user->update($validatedData['users']);
        $building->user->account->update($validatedData['accounts']);

        return redirect()->route('cooperation.admin.buildings.edit', compact('building'))->with('success', __('cooperation/admin/buildings.update.success'));
    }
}
