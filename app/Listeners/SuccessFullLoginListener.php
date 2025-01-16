<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\RedirectResponse;
use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\InputSource;
use App\Models\Log;
use App\Models\Role;
use App\Models\User;
use App\Services\Models\BuildingService;

class SuccessFullLoginListener
{
    protected BuildingService $buildingService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): ?RedirectResponse
    {
        /** @var Account $account */
        $account = $event->user;

        /** @var User $user */
        $user = $account->user();

        /** @var \App\Models\Building $building */
        $building = $user->building;

        /** @var null|Role $role */
        $role = $user->roles->first();

        // If the user for some odd reason has no role attached, attach the resident rol to him.
        if (! $role instanceof Role) {
            $residentRole = Role::findByName('resident');
            $user->assignRole($residentRole);
            /** @var Role $role */
            $role = $residentRole;
        }

        // get the input source
        $inputSource = $role->inputSource;

        // if there is only one role set for the user, and that role does not have an input source we will set it to resident.
        if (! $inputSource instanceof InputSource) {
            $inputSource = InputSource::resident();
        }

        // set the required sessions
        HoomdossierSession::setHoomdossierSessions($building, $inputSource, $inputSource, $role);

        Log::create([
            'loggable_type' => User::class,
            'loggable_id' => $user->id,
            'building_id' => $building->id,
            'message' => __('woningdossier.log-messages.logged-in', [
                'full_name' => $user->getFullName(),
            ]),
        ]);

        $this->buildingService->forBuilding($building)->forInputSource($inputSource)->performMunicipalityCheck();
        return null;
    }
}
