<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyAccountSettingsFormRequest;
use App\Jobs\CheckBuildingAddress;
use App\Models\Account;
use App\Models\InputSource;
use App\Models\Municipality;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Update the account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MyAccountSettingsFormRequest $request)
    {
        $user = Hoomdossier::user();
        $building = HoomdossierSession::getBuilding(true);

        $data = $request->validated();

        // Update user data
        $user->update($data['user']);
        // Update building address
        $data['address']['extension'] ??= null;
        $building->update($data['address']);

         CheckBuildingAddress::dispatchSync($building);
         if (! $building->municipality()->first() instanceof Municipality) {
             CheckBuildingAddress::dispatch($building);
         }

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

        // Reset master first.
        UserService::resetUser($user, InputSource::findByShort(InputSource::MASTER_SHORT));

        foreach ($inputSourceIds as $inputSourceId) {
            Log::debug("resetting for input source ".$inputSourceId);
            UserService::resetUser($user, InputSource::find($inputSourceId));
        }

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
        if ( ! $stillActiveForOtherCooperations) {
            $success = __('my-account.settings.destroy.success.full');
        }

        return redirect()->route('cooperation.welcome', compact('cooperation'))->with('success', $success);
    }
}
