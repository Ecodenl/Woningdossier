<?php

namespace App\Services;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Role;
use Illuminate\Auth\SessionGuard;

class HoomSessionGuard extends SessionGuard
{

    /**
     * Pull a user from the repository by its "remember me" cookie token.
     *
     * @param  \Illuminate\Auth\Recaller $recaller
     * @return mixed
     */
    public function userFromRecaller($recaller)
    {
        if (!$recaller->valid() || $this->recallAttempted) {
            return;
        }
        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        $this->recallAttempted = true;

        $this->viaRemember = !is_null($user = $this->provider->retrieveByToken(
            $recaller->id(), $recaller->token()
        ));

        // we need to set some sessions for the application to work properly
        if ($this->viaRemember) {

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
                if (!$role->inputSource instanceof InputSource) {
                    $inputSource = InputSource::findByShort('resident');
                }

                // set the required sessions
                HoomdossierSession::setHoomdossierSessions($building, $inputSource, $inputSource, $role);

            }
        }

        return $user;
    }
}