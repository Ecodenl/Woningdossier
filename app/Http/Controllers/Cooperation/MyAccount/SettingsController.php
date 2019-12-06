<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Events\DossierResetPerformed;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\PicoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyAccountSettingsFormRequest;
use App\Models\Account;
use App\Models\Building;
use App\Models\User;
use App\Services\UserService;

class SettingsController extends Controller
{
    /**
     * Update the account.
     *
     * @param MyAccountSettingsFormRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MyAccountSettingsFormRequest $request)
    {
        $user = Hoomdossier::user();
        $building = HoomdossierSession::getBuilding(true);

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

        return redirect()->route('cooperation.my-account.index')
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
        $building->completedSteps()->delete();
        // remove the step comments
        $building->stepComments()->delete();
        // remove the answers on the custom questionnaires
        $building->questionAnswers()->delete();

        // remove the action plan advices from the user
        $user->actionPlanAdvices()->delete();
        // remove the user interests
        $user->userInterests()->delete();
        // remove the energy habits from a user
        $user->energyHabit()->delete();
        // remove the motivations from a user
        $user->motivations()->delete();
        // detach the progress of the completed questionnaires
        // belongstomany, so dont delete!
        $user->completedQuestionnaires()->detach();

        DossierResetPerformed::dispatch($building);

        return redirect()->back()->with('success', __('my-account.settings.reset-file.success'));
    }

    // Delete account
    public function destroy()
    {
        $user = Hoomdossier::user();
        $accountId = $user->account_id;
        $cooperation = HoomdossierSession::getCooperation(true);

        UserService::deleteUser($user);

        // delete, destroy and invalidate all session stuff.
        HoomdossierSession::destroy();
        \Auth::logout();
        request()->session()->invalidate();

        $stillActiveForOtherCooperations = Account::where('id', '=', $accountId)->exists();
        $success = __('my-account.settings.destroy.success.cooperation');
        if (! $stillActiveForOtherCooperations) {
            $success = __('my-account.settings.destroy.success.full');
        }

        return redirect()->route('cooperation.welcome', compact('cooperation'))->with('success', $success);
    }
}
