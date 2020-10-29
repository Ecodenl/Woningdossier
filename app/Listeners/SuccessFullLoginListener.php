<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Log;
use App\Models\Role;
use App\Models\User;

class SuccessFullLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param $event
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($event)
    {
        $account = $event->user;

        $this->ensureCooperationIsSet();

        /** @var User $user */
        // cooperation is set, so we can safely retrieve the user from the account.
        $user = $account->user();
        // get the first building from the user
        $building = $user->building;
        // just get the first available role from the user
        $userRole = $user->roles()->first();

        // if the user for some odd reason had no role attached, attach the resident rol to him.
        if (! $userRole instanceof Role) {
            $residentRole = Role::findByName('resident');
            $user->assignRole($residentRole);
        }

        // if the user has a building, log him in.
        // else, redirect him to a page where he needs to create a building
        // without a building the application is useless.
        // we cant query on the Spatie\Role model so we first get the result on the "original model"
        $role = Role::findByName($user->roles()->first()->name);

        // get the input source
        $inputSource = $role->inputSource;

        // if there is only one role set for the user, and that role does not have an input source we will set it to resident.
        if (! $role->inputSource instanceof InputSource) {
            $inputSource = InputSource::findByShort('resident');
        }

        // set the required sessions
        HoomdossierSession::setHoomdossierSessions($building, $inputSource, $inputSource, $role);

        Log::create([
            'building_id' => $building->id,
            'user_id' => $user->id,
            'message' => __('woningdossier.log-messages.logged-in', [
                'full_name' => $user->getFullName(),
            ]),
        ]);
    }

    /**
     * Method to ensure the cooperation is set in the session.
     *
     * @return void
     */
    private function ensureCooperationIsSet()
    {
        /** @var Cooperation|string $cooperation */
        $cooperation = request()->route('cooperation');

        // if the user logs in through the remember the router is not booted yet.
        if (! $cooperation instanceof Cooperation) {
            $cooperation = Cooperation::where('slug', $cooperation)->first();
        }

        if ($cooperation instanceof Cooperation) {
            HoomdossierSession::setCooperation($cooperation);
        }

        // this boots before the router, so we check if the request contains deleted cooperations
        // and log them out and forget the remember me cookie
        if (in_array(request()->route('cooperation'), ['vrijstadenergie', 'hnwr'])) {
            \Auth::logout();
        }
    }
}
