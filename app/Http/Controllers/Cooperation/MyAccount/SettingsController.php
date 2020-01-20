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
use App\Models\InputSource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

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
    public function resetFile(Request $request)
    {
        $user = Hoomdossier::user();

        $inputSourceIds = $request->input('input_sources.id');

        foreach ($inputSourceIds as $inputSourceId) {
            UserService::resetUser($user, InputSource::find($inputSourceId));
        }

        DossierResetPerformed::dispatch($user->building);

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
