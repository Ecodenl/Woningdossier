<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\AccountFormRequest;
use App\Models\Account;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;

class AccountController extends Controller
{
    public function disableTwoFactorAuthentication(AccountFormRequest $request, DisableTwoFactorAuthentication $disable): RedirectResponse
    {
        $accountId = $request->validated()['accounts']['id'];
        $account   = Account::findOrFail($accountId);

        $disable($account);

        $building = $account->user()->building;
        return redirect()->route('cooperation.admin.buildings.show', compact('building'))
            ->withFragment('2fa')
            ->with('success', __('general.2fa.disabled'));
    }
}
