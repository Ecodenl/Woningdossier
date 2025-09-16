<?php

namespace App\Traits\Http;

use App\Events\ParticipantAddedEvent;
use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\Hoomdossier;
use App\Helpers\Str;
use App\Http\Requests\Cooperation\Admin\Cooperation\UserFormRequest;
use App\Mail\UserCreatedEmail;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use App\Services\UserService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

trait CreatesUsers
{
    public function createUser(UserFormRequest $request, Cooperation $cooperation): void
    {
        // give the user his role
        $roleIds = $request->input('roles', '');
        $roles = [];
        $requestData = $request->validated();

        // So, in the old way we just threw everything in one pile and that was processed. Now we (try to) put
        // everything separated by database table (e.g. accounts.email, users.first_name). To accommodate this change,
        // it's just thrown together (for now!).
        $input = array_merge(
            $requestData['accounts'],
            $requestData['users'],
            [
                'address' => $requestData['address'],
            ]
        );

        // Add a random password to the data (when not local) so the user must first do a password reset.
        $input['password'] = Hash::make(
            App::isLocal() ? 'password' : Str::randomPassword()
        );

        foreach ($roleIds as $roleId) {
            $role = \App\Models\Role::findOrFail($roleId);
            Gate::authorize('view', [$role, Hoomdossier::user(), \App\Helpers\HoomdossierSession::getRole(true)]);
            $roles[] = $role->name;
        }

        $user = UserService::register($cooperation, $roles, $input);
        $account = $user->account;
        $building = $user->building;

        // at this point, a user cant register without accepting the privacy terms.
        UserAllowedAccessToHisBuilding::dispatch($user, $building);

        // if the created user is a resident, then we connect the selected coach to the building, else we dont.
        if ($request->has('coach_id')) {
            // find the coach to give permissions
            $coach = User::forAllCooperations()->find($request->input('coach_id'));

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
     */
    public function sendAccountConfirmationMail(Cooperation $cooperation, Account $account, User $user): void
    {
        $token = app('auth.password.broker')->createToken($account);

        // send a mail to the user
        Mail::to($account->email)->send(new UserCreatedEmail($cooperation, $user, $token));
    }
}
