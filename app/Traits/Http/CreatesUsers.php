<?php

namespace App\Traits\Http;

use App\Events\ParticipantAddedEvent;
use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\Hoomdossier;
use App\Helpers\Str;
use App\Mail\UserCreatedEmail;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

trait CreatesUsers
{
    public function createUser(Request $request, Cooperation $cooperation)
    {
        // give the user his role
        $roleIds = $request->get('roles', '');
        $roles = [];
        $requestData = $request->all();
        // add a random password to the data
        $requestData['password'] = Hash::make(Str::randomPassword());

        foreach ($roleIds as $roleId) {
            $role = \App\Models\Role::findOrFail($roleId);
            $this->authorize('view', [$role, Hoomdossier::user(), \App\Helpers\HoomdossierSession::getRole(true)]);
            $roles[] = $role->name;
        }


        $user = UserService::register($cooperation, $roles, $requestData);
        $account = $user->account;
        $building = $user->building;

        // at this point, a user cant register without accepting the privacy terms.
        UserAllowedAccessToHisBuilding::dispatch($user, $building);

        // if the created user is a resident, then we connect the selected coach to the building, else we dont.
        if ($request->has('coach_id')) {
            // find the coach to give permissions
            $coach = User::find($request->get('coach_id'));

            // now give the selected coach access with permission to the new created building
            BuildingPermissionService::givePermission($coach, $building);
            BuildingCoachStatusService::giveAccess($coach, $building);

            // dispatch an event so the user is notified
            ParticipantAddedEvent::dispatch($coach, $building, $request->user(), $cooperation);
        }

        // if the account is recently created we have to send a confirmation mail
        // else we send a notification to the user he is associated with a new cooperation
        if ($account->wasRecentlyCreated) {
            // and send the account confirmation mail.
            $this->sendAccountConfirmationMail($cooperation, $account, $user);
            $account->markEmailAsVerified();
        } else {
            UserAssociatedWithOtherCooperation::dispatch($cooperation, $user);
        }
    }

    /**
     * Send the mail to the created user.
     *
     * @param Request $request
     */
    public function sendAccountConfirmationMail(Cooperation $cooperation, Account $account, User $user)
    {
        $token = app('auth.password.broker')->createToken($account);

        // send a mail to the user
        Mail::to($account->email)->send(new UserCreatedEmail($cooperation, $user, $token));
    }
}