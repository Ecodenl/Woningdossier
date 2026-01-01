<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Enums\ApiImplementation;
use App\Events\UserToolDataChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyAccountSettingsFormRequest;
use App\Jobs\CheckBuildingAddress;
use App\Jobs\ResetDossierForUser;
use App\Models\Account;
use App\Models\InputSource;
use App\Models\Municipality;
use App\Services\DossierSettingsService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Update the account.
     */
    public function update(MyAccountSettingsFormRequest $request): RedirectResponse
    {
        $user = Hoomdossier::user();
        $cooperation = $user->cooperation;
        $building = HoomdossierSession::getBuilding(true);

        $data = $request->validated();

        // Update user data
        $user->update($data['user']);
        // Update building address
        $data['address']['extension'] ??= null;
        $building->update($data['address']);

        $currentInputSource = HoomdossierSession::getInputSource(true);

        if ($cooperation->getCountry()->supportsApi(ApiImplementation::LV_BAG)) {
            CheckBuildingAddress::dispatchSync($building, $currentInputSource);
            if (! $building->municipality()->first() instanceof Municipality) {
                CheckBuildingAddress::dispatch($building, $currentInputSource);
            }
        }

        return to_route('cooperation.my-account.index')
            ->with('success', __('my-account.settings.store.success'));
    }

    /**
     * Reset the user his plan / file / dossier.
     */
    public function resetFile(Request $request, DossierSettingsService $dossierSettingsService): RedirectResponse
    {
        $user = Hoomdossier::user();

        $inputSourceIds = $request->input('input_sources.id');

        $masterInputSource = InputSource::master();
        ResetDossierForUser::dispatchSync($user, $masterInputSource);
        foreach ($inputSourceIds as $inputSourceId) {
            Log::debug("resetting for input source " . $inputSourceId);
            $relevantInputSource = InputSource::find($inputSourceId);
            ResetDossierForUser::dispatchSync($user, $relevantInputSource);
        }

        $dossierSettingsService
            ->forType(ResetDossierForUser::class)
            ->forBuilding($user->building)
            ->forInputSource($masterInputSource)
            ->justDone();

        UserToolDataChanged::dispatch($user);

        return redirect()->back()->with('success', __('my-account.settings.reset-file.success'));
    }

    // Delete account
    public function destroy(): RedirectResponse
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

        return to_route('cooperation.welcome', compact('cooperation'))->with('success', $success);
    }
}
