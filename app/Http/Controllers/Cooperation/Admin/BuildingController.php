<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Models\PrivateMessageView;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Enums\ApiImplementation;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\BuildingFormRequest;
use App\Jobs\CheckBuildingAddress;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Log;
use App\Models\Municipality;
use App\Models\PrivateMessage;
use App\Models\Scan;
use App\Models\Status;
use App\Models\User;
use App\Helpers\SmallMeasuresSettingHelper;
use App\Services\BuildingCoachStatusService;
use App\Services\UserRoleService;
use App\Models\Role;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * Handles the data for the show user for a coach, coordinator and cooperation-admin.
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(UserRoleService $userRoleService, Cooperation $cooperation, Building $building): View|RedirectResponse
    {
        $user = $building->user;

        if ($building->trashed() || ! $user instanceof User) {
            return redirect()->route('cooperation.admin.index');
        }

        Gate::authorize('show', [$building]);

        $buildingId = $building->id;

        $roles = Role::all();

        $coaches = $cooperation->getCoaches();

        $statuses = Status::ordered()->get();

        $coachesWithActiveBuildingCoachStatus = BuildingCoachStatusService::getConnectedCoachesByBuilding($building);

        $mostRecentStatus = $building->getMostRecentBuildingStatus();

        $logs = Log::forBuildingId($buildingId)->get();

        $privateMessages = PrivateMessage::private()->conversation($buildingId)->get();
        $publicMessages = PrivateMessage::public()->conversation($buildingId)->get();
        $messageCountSplit = PrivateMessageView::getTotalUnreadMessagesForCurrentRole(true, [['building_id', $building->id]]);

        // get all the building notes
        $buildingNotes = $building->buildingNotes()->orderByDesc('updated_at')->get();

        $scan = $cooperation->scans()->where('short', '!=', Scan::EXPERT)->first();
        $scans = $cooperation->load(['scans' => fn($q) => $q->where('short', '!=', Scan::EXPERT)])->scans;
        $userCurrentRole = HoomdossierSession::getRole(true);

        // Haal small measures instellingen op per scan
        $smallMeasuresSettings = [];
        foreach ($scans as $scanItem) {
            $smallMeasuresSettings[$scanItem->short] = [
                'cooperation_enabled' => SmallMeasuresSettingHelper::isEnabledForCooperation($cooperation, $scanItem),
                'building_override' => SmallMeasuresSettingHelper::hasOverride($building, $scanItem),
                'effective' => SmallMeasuresSettingHelper::isEnabledForBuilding($building, $scanItem),
            ];
        }

        return view('cooperation.admin.buildings.show', compact(
            'userRoleService',
            'userCurrentRole',
            'user',
            'building',
            'roles',
            'coaches',
            'scans',
            'coachesWithActiveBuildingCoachStatus',
            'mostRecentStatus',
            'privateMessages',
            'publicMessages',
            'messageCountSplit',
            'buildingNotes',
            'statuses',
            'logs',
            'scan',
            'smallMeasuresSettings',
        ));
    }

    public function updateSmallMeasuresSetting(
        Request $request,
        Cooperation $cooperation,
        Building $building
    ): RedirectResponse
    {
        Gate::authorize('show', [$building]);

        $validated = $request->validate([
            'scan_short' => ['required', 'in:quick-scan,lite-scan'],
            'enabled' => ['required'],
        ]);

        $scan = Scan::findByShort($validated['scan_short']);
        $enabled = filter_var($validated['enabled'], FILTER_VALIDATE_BOOLEAN);

        // Alleen toestaan als cooperatie instelling UIT staat
        if (SmallMeasuresSettingHelper::isEnabledForCooperation($cooperation, $scan)) {
            return redirect()->back()
                ->with('warning', __('cooperation/admin/buildings.small-measures.error.cooperation-enabled'));
        }

        SmallMeasuresSettingHelper::setOverride($building, $scan, $enabled);

        return redirect()->back()
            ->with('success', __('cooperation/admin/buildings.small-measures.success'));
    }

    public function edit(Cooperation $cooperation, Building $building): View
    {
        $user = $building->user()->with('account')->first();
        $account = $user->account;

        return view('cooperation.admin.buildings.edit', compact('building', 'user', 'account'));
    }

    public function update(BuildingFormRequest $request, Cooperation $cooperation, Building $building): RedirectResponse
    {
        $validatedData = $request->validated();
        if (! is_null($validatedData['users']['extra']['contact_id'] ?? null)) {
            // Force as INT
            $validatedData['users']['extra']['contact_id'] = (int) $validatedData['users']['extra']['contact_id'];
        }

        $validatedData['address']['extension'] ??= null;
        $building->update($validatedData['address']);

        $buildingFeature = $building->buildingFeatures()->allInputSources()
            ->whereHas('inputSource', fn ($q) => $q->whereNotIn('input_source_id', [InputSource::master()->id, InputSource::exampleBuilding()->id]))
            ->orderByDesc('updated_at')
            ->first();

        $inputSource = $buildingFeature->inputSource ?? InputSource::resident();

        if ($cooperation->getCountry()->supportsApi(ApiImplementation::LV_BAG)) {
            CheckBuildingAddress::dispatchSync($building, $inputSource);
            if (! $building->municipality()->first() instanceof Municipality) {
                CheckBuildingAddress::dispatch($building, $inputSource);
            }
        }

        $validatedData['users']['phone_number'] = $validatedData['users']['phone_number'] ?? '';
        $building->user->update($validatedData['users']);
        $building->user->account->update($validatedData['accounts']);

        return redirect()->route('cooperation.admin.buildings.edit', compact('building'))
            ->with('success', __('cooperation/admin/buildings.update.success'));
    }
}
