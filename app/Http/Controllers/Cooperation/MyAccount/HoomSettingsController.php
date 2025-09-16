<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use Illuminate\Http\RedirectResponse;
use App\Events\UserChangedHisEmailEvent;
use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\MyAccount\HoomSettingsRequest;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class HoomSettingsController extends Controller
{
    /**
     * Update the account settings.
     */
    public function update(HoomSettingsRequest $request): RedirectResponse
    {
        $user = Hoomdossier::user();
        $account = Hoomdossier::account();

        $data = $request->all();

        $accountData = $data['account'];

        // Hash the password
        $accountData['password'] = Hash::make($accountData['password']);

        // If the password from the request is empty, we unset all password key values from the array.
        if (empty($request->input('account.password'))) {
            unset($accountData['password'], $accountData['password_confirmation'], $accountData['current_password']);
        }

        // Check if the user changed his email. If so, we set the old email
        // and send the user an email so he can change it back.
        if ($account->email != $accountData['email']) {
            Event::dispatch(new UserChangedHisEmailEvent($user, $account, $account->email, $accountData['email']));
        }

        // Update the account data
        $account->update($accountData);

        return redirect()->route('cooperation.my-account.index')
            ->with('success', __('my-account.hoom-settings.store.success'));
    }
}
