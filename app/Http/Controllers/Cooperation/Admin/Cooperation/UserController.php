<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Events\ParticipantAddedEvent;
use App\Helpers\PicoHelper;
use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\UserRequest;
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
            $users = $cooperation
                ->users()
                ->get();
        $roles = Role::all();

        return view('cooperation.admin.cooperation.users.index', compact('roles', 'users'));
    }

    public function create(Cooperation $cooperation)
    {
        $roles = Role::where('name', 'coach')->orWhere('name', 'resident')->get();
        $coaches = $cooperation->getCoaches()->get();

        return view('cooperation.admin.cooperation.users.create', compact('roles', 'coaches'));
    }


    public function store(Cooperation $cooperation, UserRequest $request)
    {
        $firstName = $request->get('first_name', '');
        $lastName = $request->get('last_name', '');
        $email = $request->get('email', '');

        $postalCode = trim(strip_tags($request->get('postal_code', '')));
        $houseNumber = trim(strip_tags($request->get('number', '')));
        $extension = trim(strip_tags($request->get('house_number_extension', '')));

        $street = strip_tags($request->get('street', ''));
        $city = trim(strip_tags($request->get('city')));
        $addressId = $request->get('addressid', null);
        $coachId = $request->get('coach_id', '');

        // create the user
        $user = User::create(
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]
        );

        $user->account()->associate(
            Account::create(
                [
                    'email' => $email,
                    'password' => \Hash::make(Str::randomPassword()),
                ]
            )
        )->save();

        // now get the pico address data.
        $picoAddressData = PicoHelper::getAddressData(
            $postalCode, $houseNumber
        );

        $features = new BuildingFeature([
            'surface' => empty($picoAddressData['surface']) ? null : $picoAddressData['surface'],
            'build_year' => empty($picoAddressData['build_year']) ? null : $picoAddressData['build_year'],
        ]);

        // make a new building
        $building = new Building(
            [
                'street' => $street,
                'number' => $houseNumber,
                'extension' => $extension,
                'postal_code' => $postalCode,
                'city' => $city,
                'bag_addressid' => $picoAddressData['id'] ?? $addressId  ?? ''
            ]
        );

        // save the building feature and the building itself and accociate the new user with it
        $building->user()->associate($user)->save();
        $features->building()->associate($building)->save();

        // give the user his role
        $roleIds = $request->get('roles', '');
        $roles = [];
        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            array_push($roles, $role->name);
        }

        // attach the new user to the cooperation
        $user->cooperation()->associate($cooperation)->save();


        // assign the roles to the user
        $user->assignRole($roles);

        // if the created user is a resident, then we connect the selected coach to the building, else we dont.
        if ($request->has('coach_id')) {
            // so create a message, with the access allowed
            PrivateMessage::create(
                [
                    // we get the selected option from the language file, we can do this cause the submitted value = key from localization
                    'is_public' => true,
                    'message' => '',
                    'from_user_id' => $user->id,
                    'from_user' => $user->getFullName(),
                    'to_cooperation_id' => $cooperation->id,
                    'building_id' => $building->id,
                    'request_type' => 'user-created-by-cooperation',
                    'allow_access' => true,
                ]
            );

            $coach = User::find($coachId);
            // now give the selected coach access with permission to the new created building
            BuildingPermissionService::givePermission($coachId, $building->id);
            BuildingCoachStatusService::giveAccess($coachId, $building->id);

            // and fire the added event twice, for the user itself and for the coach.
            event(new ParticipantAddedEvent($user, $building));
            event(new ParticipantAddedEvent($coach, $building));
        }
        // and send the account confirmation mail.
        $this->sendAccountConfirmationMail($cooperation, $request);

        return redirect()
            ->route('cooperation.admin.cooperation.users.index')
            ->with('success', __('woningdossier.cooperation.admin.cooperation.users.store.success'));
    }

    /**
     * Send the mail to the created user.
     *
     * @param Cooperation $cooperation
     * @param Request $request
     */
    public function sendAccountConfirmationMail(Cooperation $cooperation, Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();

        $token = app('auth.password.broker')->createToken($user);

        // send a mail to the user
        \Mail::to($user->email)->sendNow(new UserCreatedEmail($cooperation, $user, $token));
    }

    /**
     * Destroy a user.
     *
     * @param Cooperation $cooperation
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
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
