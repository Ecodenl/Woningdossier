<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cooperation\Coordinator\CoachRequest;
use App\Mail\UserCreatedEmail;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation->users()->where('id', '!=', \Auth::id())->get();
        $roles = Role::all();

        return view('cooperation.admin.cooperation.coordinator.user.index', compact('roles', 'users'));
    }

    public function create()
    {
        $roles = Role::where('name', 'coach')->orWhere('name', 'resident')->get();

        return view('cooperation.admin.cooperation.coordinator.user.create', compact('roles'));
    }

    protected function getAddressData($postalCode, $number, $pointer = null)
    {
        \Log::debug($postalCode.' '.$number.' '.$pointer);
        /** @var PicoClient $pico */
        $pico = app()->make('pico');
        $postalCode = str_replace(' ', '', trim($postalCode));
        $response = $pico->bag_adres_pchnr(['query' => ['pc' => $postalCode, 'hnr' => $number]]);

        if (! is_null($pointer)) {
            foreach ($response as $addrInfo) {
                if (array_key_exists('bag_adresid', $addrInfo) && $pointer == md5($addrInfo['bag_adresid'])) {
                    //$data['bag_addressid'] = $addrInfo['bag_adresid'];
                    \Log::debug(json_encode($addrInfo));

                    return $addrInfo;
                }
            }

            return [];
        }

        return $response;
    }

    public function store(Cooperation $cooperation, CoachRequest $request)
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

        // create the new user
        $user = User::create(
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => bcrypt(Str::randomPassword()),
            ]
        );

        // get the address information from the bag
        $address = $this->getAddressData($postalCode, $houseNumber, $addressId);

        // make building features
        $features = new BuildingFeature(
            [
                'surface' => array_key_exists('adresopp', $address) ? $address['adresopp'] : null,
                'build_year' => array_key_exists('bouwjaar', $address) ? $address['bouwjaar'] : null,
            ]
        );

        // make a new building
        $address = new Building(
            [
                'street' => $street,
                'number' => $houseNumber,
                'extension' => $extension,
                'postal_code' => $postalCode,
                'city' => $city,
                'bag_addressid' => isset($address['bag_adresid']) ? $address['bag_adresid'] : '',
            ]
        );

        // save the building feature and the building itself and accociate the new user with it
        $address->user()->associate($user)->save();
        $features->building()->associate($address)->save();

        // give the user his role
        $roleIds = $request->get('roles', '');
        $roles = [];
        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            array_push($roles, $role->name);
        }

        // attach the new user to the cooperation
        $user->cooperations()->attach($cooperation->id);
        // assign the roles to the user
        $user->assignRole($roles);

        $this->sendAccountConfirmationMail($cooperation, $request);

        return redirect()
            ->route('cooperation.admin.cooperation.coordinator.user.index')
            ->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.user.store.success'));
    }

    /**
     * Send the mail to the created user.
     *
     * @param Cooperation $cooperation
     * @param Request     $request
     */
    public function sendAccountConfirmationMail(Cooperation $cooperation, Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();

        $token = app('auth.password.broker')->createToken($user);

        // send a mail to the user
        \Mail::to($user->email)->sendNow(new UserCreatedEmail($cooperation, $user, $token));
    }

    public function destroy(Cooperation $cooperation, $userId)
    {
        $user = $cooperation->users()->findOrFail($userId);

        // only remove the example building id from the building
        if ($user->buildings()->first() instanceof Building) {
            $building = $user->buildings()->first();
            $building->example_building_id = null;
            $building->save();
            // delete the services from a building
            $building->buildingServices()->delete();
            // delete the elements from a building
            $building->buildingElements()->delete();
            // remove the features from a building
            $building->buildingFeatures()->delete();
            // remove the roof types from a building
            $building->roofTypes()->delete();
            // remove the heater from a building
            $building->heater()->delete();
            // remove the solar panels from a building
            $building->pvPanels()->delete();
            // remove the insulated glazings from a building
            $building->currentInsulatedGlazing()->delete();
            // remove the paintwork from a building
            $building->currentPaintworkStatus()->delete();
            // remove the user usage from a building
            $building->userUsage()->delete();
        }
        // remove the building usages from the user
        $user->buildingUsage()->delete();
        // remove the action plan advices from the user
        $user->actionPlanAdvices()->delete();
        // remove the user interests
        $user->interests()->delete();
        // remove the energy habits from a user
        $user->energyHabit()->delete();
        // remove the motivations from a user
        $user->motivations()->delete();
        // remove the progress from a user
        $user->progress()->delete();

        $user->cooperations()->detach($cooperation->id);

        $user->delete();

        return redirect()->back()->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.user.destroy.success'));
    }
}
