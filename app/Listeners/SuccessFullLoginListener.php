<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Log;
use App\Models\Role;
use Carbon\Carbon;

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
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;
        // get the first building from the user
        $building = $user->buildings()->first();

        // if the user has a building, log him in.
        // else, redirect him to a page where he needs to create a building
        // without a building the application is useless.
        if ($building instanceof Building) {
            // we cant query on the Spatie\Role model so we first get the result on the "original model"
            $role = Role::findByName($user->roles->first()->name);

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
                    'time' => Carbon::now(),
                ])
            ]);

        }
        if (\Auth::viaRemember()) {
            \Log::debug('User logged in with a remember token! user id: '.$user->id);
        }
    }
}
