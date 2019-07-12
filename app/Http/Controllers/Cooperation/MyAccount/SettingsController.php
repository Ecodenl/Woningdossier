<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Events\UserChangedHisEmailEvent;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\PicoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyAccountSettingsFormRequest;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\Log;
use App\Models\OldEmail;
use App\Models\User;
use App\Services\UserService;
use function GuzzleHttp\Psr7\uri_for;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Hoomdossier::user();
        $account = Hoomdossier::account();
        $building = Building::find(HoomdossierSession::getBuilding());

        return view('cooperation.my-account.settings.index', compact('user', 'building', 'account'));
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
        $user = Hoomdossier::user();
        $building = Building::find(HoomdossierSession::getBuilding());

        $data = $request->all();

        $buildingData = $data['building'];
        $userData = $data['user'];

        // now get the pico address data.
        $picoAddressData = PicoHelper::getAddressData(
            $buildingData['postal_code'], $buildingData['house_number']
        );

        $userData['phone_number'] = $userData['phone_number'] ?? '';

        $buildingData['extension'] = $buildingData['extension'] ?? '';

        $buildingData['number'] = $buildingData['house_number'] ?? '';
        // try to obtain the address id from the api, else get the one from the request.
        $buildingData['bag_addressid'] = $picoAddressData['id'] ?? $buildingData['addressid'] ?? '';


        // update the user stuff
        $user->update($userData);
        // now update the building itself
        $building->update($buildingData);

        // and update the building features with the data from pico.
        $building->buildingFeatures()->update([
            'surface' => empty($picoAddressData['surface']) ? null : $picoAddressData['surface'],
            'build_year' => empty($picoAddressData['build_year']) ? null : $picoAddressData['build_year'],
        ]);


        return redirect()->route('cooperation.my-account.settings.index')
                         ->with('success', __('my-account.settings.store.success'));
    }

    /**
     * Reset the user his plan / file / dossier.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetFile()
    {
        $user = Hoomdossier::user();

        // only remove the example building id from the building
        $building = $user->building;
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
        // remove all progress made in the tool
        $building->progress()->delete();

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

        return redirect()->back()->with('success', __('my-account.settings.reset-file.success'));
    }

    // Delete account
    public function destroy()
    {
        $user = \App\Helpers\Hoomdossier::user();

        UserService::deleteUser($user);

        // delete, destroy and invalidate all session stuff.
        HoomdossierSession::destroy();
        \Auth::logout();
        request()->session()->invalidate();


        return redirect(url(''));
    }
}
