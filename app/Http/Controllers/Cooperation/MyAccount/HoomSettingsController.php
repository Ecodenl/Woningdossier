<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Events\UserChangedHisEmailEvent;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\MyAccount\HoomSettingsRequest;
use App\Models\Building;
use App\Models\Cooperation;
use App\Http\Controllers\Controller;

class HoomSettingsController extends Controller
{
    /**
     * Update the account settings
     *
     * @param  HoomSettingsRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(HoomSettingsRequest $request)
    {
        $user = Hoomdossier::user();
        $account = Hoomdossier::account();

        $data = $request->all();

        $accountData = $data['account'];

        // hash the password
        $accountData['password'] = \Hash::make($accountData['password']);

        // if the password from the request is empty, we unset all password key values from the array.
        if (empty($request->input('account.password'))) {
            unset($accountData['password'], $accountData['password_confirmation'], $accountData['current_password']);
        }

        // check if the user changed his email, if so. We set the old email and send the user a email so he can change it back.
        if ($account->email != $accountData['email']) {
            \Event::dispatch(new UserChangedHisEmailEvent($user, $account, $account->email, $accountData['email']));
        }

        // update the account data
        $account->update($accountData);


        return redirect()->route('cooperation.my-account.index')
                         ->with('success', __('my-account.hoom-settings.store.success'));

    }
}
