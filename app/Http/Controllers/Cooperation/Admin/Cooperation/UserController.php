<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Events\ParticipantAddedEvent;
use App\Events\Registered;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\Hoomdossier;
use App\Helpers\PicoHelper;
use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\UserFormRequest;
use App\Mail\UserAssociatedWithCooperation;
use App\Mail\UserCreatedEmail;
use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        // change the relationship to building on merge.
        $users = $cooperation
            ->users()
            ->whereHas('building')
            ->with(['building' => function ($query) {
                $query->with(['buildingStatuses' => function ($query) {
                    $query->mostRecent();
                }]);
            }])->get();
        $roles = Role::all();

        return view('cooperation.admin.cooperation.users.index', compact('roles', 'users'));
    }

    public function create(Cooperation $cooperation)
    {
        $possibleRoles = Role::all();
        $roles = [];
        foreach ($possibleRoles as $possibleRole) {
            if (Hoomdossier::user()->can('assign-role', $possibleRole)) {
                $roles[] = $possibleRole;
            }
        }
        $roles = collect($roles);
        $coaches = $cooperation->getCoaches()->get();

        return view('cooperation.admin.cooperation.users.create', compact('roles', 'coaches'));
    }

    public function store(UserFormRequest $request, Cooperation $cooperation)
    {

        // give the user his role
        $roleIds = $request->get('roles', '');

        $roles = [];
        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            if (Hoomdossier::user()->can('assign-role', $role)) {
                \Log::debug('User can assign role '.$role->name);
                array_push($roles, $role->name);
            }
        }

        $requestData = $request->all();
        // add a random password to the data
        $requestData['password'] = \Hash::make(Str::randomPassword());
        $user = UserService::register($cooperation, $roles, $requestData);
        $account = $user->account;
        $building = $user->building;


        // we always have to set the access to true when a user is created through the admin environment
        PrivateMessage::create(
            [
                // we get the selected option from the language file, we can do this cause the submitted value = key from localization
                'is_public' => true,
                'message' => __('woningdossier.cooperation.admin.cooperation.users.store.private-message-allowed-access'),
                'from_user_id' => $user->id,
                'from_user' => $user->getFullName(),
                'to_cooperation_id' => $cooperation->id,
                'building_id' => $building->id,
                'request_type' => 'user-created-by-cooperation',
                'allow_access' => true,
            ]
        );




        // if the created user is a resident, then we connect the selected coach to the building, else we dont.
        if ($request->has('coach_id')) {

            // find the coach to give permissions
            $coach = User::find($request->get('coach_id'));

            // now give the selected coach access with permission to the new created building
            BuildingPermissionService::givePermission($coach, $building);
            BuildingCoachStatusService::giveAccess($coach, $building);

            // dispatch an event so the user is notified
            ParticipantAddedEvent::dispatch($coach, $building);
        }


        // if the account is recently created we have to send a confirmation mail
        // else we send a notification to the user he is associated with a new cooperation
        if ($account->wasRecentlyCreated) {
            // and send the account confirmation mail.
            $this->sendAccountConfirmationMail($cooperation, $account);
            $account->confirm();
        } else {
            $this->sendAccountAssociatedMail($cooperation, $account);
        }

        return redirect()
            ->route('cooperation.admin.cooperation.users.index')
            ->with('success', __('woningdossier.cooperation.admin.cooperation.users.store.success'));
    }

    /**
     * Send the mail to the created user.
     *
     * @param Cooperation $cooperation
     * @param Request     $request
     */
    public function sendAccountConfirmationMail(Cooperation $cooperation, Account $account)
    {
        $token = app('auth.password.broker')->createToken($account);

        // send a mail to the user
        \Mail::to($account->email)->sendNow(new UserCreatedEmail($cooperation, $account->user(), $token));
    }

    public function sendAccountAssociatedMail(Cooperation $cooperation, Account $account)
    {
        \Mail::to($account->email)->sendNow(new UserAssociatedWithCooperation($cooperation, $account->user()));
    }

    /**
     * Destroy a user.
     *
     * @param Cooperation $cooperation
     * @param Request     $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Cooperation $cooperation, Request $request)
    {
        $userId = $request->get('user_id');

        $user = User::find($userId);

        $this->authorize('destroy', $user);

        if ($user instanceof User) {
            UserService::deleteUser($user);
        }
    }
}
