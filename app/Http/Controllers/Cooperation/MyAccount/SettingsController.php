<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Events\UserChangedHisEmailEvent;
use App\Helpers\HoomdossierSession;
use App\Helpers\PicoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyAccountSettingsFormRequest;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Services\UserService;
use function GuzzleHttp\Psr7\uri_for;

class SettingsController extends Controller
{
    public function index()
    {
        $user = \Auth::user();
        $building = Building::find(HoomdossierSession::getBuilding());

        return view('cooperation.my-account.settings.index', compact('user', 'building'));
    }

    /**
     * Update the account.
     *
     * @param  MyAccountSettingsFormRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MyAccountSettingsFormRequest $request)
    {
        $user = \Auth::user();
        $building = Building::find(HoomdossierSession::getBuilding());

        $data = $request->all();

        $buildingData = $data['building'];
        $userData = $data['user'];

        // not allowed in the current state
        // if this happens the user is doing something fishy, so just redirect him back.
        if (array_key_exists('email', $userData)) {
            return redirect()->route('cooperation.my-account.settings.index');
        }

        // now get the pico address data.
        $picoAddressData = PicoHelper::getAddressData(
            $buildingData['postal_code'], $buildingData['house_number']
        );

        $userData['phone_number'] = $userData['phone_number'] ?? '';

        $buildingData['extension'] = $buildingData['extension'] ?? '';
        $buildingData['number'] = $buildingData['house_number'] ?? '';
        // try to obtain the address id from the api, else get the one from the request.
        $buildingData['bag_addressid'] = $picoAddressData['id'] ?? $buildingData['addressid'];


        // if the password is empty we remove all the password stuff from the user data
        // else we do some checks and hash it!
        if (empty($userData['password'])) {
            unset($userData['password'], $userData['password_confirmation'], $userData['current_password']);
        } else {
            $currentPassword = $user->password;
            $currentPasswordFromRequestToCheck = $userData['current_password'];

            if (!\Hash::check($currentPasswordFromRequestToCheck, $currentPassword)) {
                return redirect()->back()->withErrors(['current_password' => __('validation.current_password')]);
            }
            $userData['password'] = \Hash::make($userData['password']);
        }

        // check if the user changed his email, if so we send a confirmation to the user itself.
        //        if ($user->email != $userData['email']) {
        //            event(new UserChangedHisEmailEvent($user->email, $userData['email']));
        //        }

        // update the user stuff
        $user->update($userData);


        // now update the building itself.
        $building->update($buildingData);
        // and update the building features with the data from pico.
        $building->buildingFeatures()->update([
            'surface' => $picoAddressData['surface'] ?? null,
            'build_year' => $picoAddressData['build_year'] ?? null,
        ]);


        return redirect()->route('cooperation.my-account.settings.index')->with('success', __('woningdossier.cooperation.my-account.settings.store.success'));
    }

    /**
     * Reset the user his plan / file / dossier.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetFile()
    {
        $user = \Auth::user();

        // only remove the example building id from the building
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
        // remove all progress made in the tool
        $building->progress()->delete();

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
        //$user->progress()->delete();

        return redirect()->back()->with('success', __('woningdossier.cooperation.my-account.settings.form.reset-file.success'));
    }

    // Delete account
    public function destroy()
    {
        $user = \Auth::user();

        UserService::deleteUser($user);

        return redirect(url(''));
    }
}
