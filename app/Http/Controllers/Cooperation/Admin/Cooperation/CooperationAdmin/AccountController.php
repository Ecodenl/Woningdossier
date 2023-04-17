<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\AccountFormRequest;
use App\Models\Account;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;

class AccountController extends Controller
{
    public function disableTwoFactorAuthentication(AccountFormRequest $request, DisableTwoFactorAuthentication $disable)
    {
        $accountId = $request->validated()['accounts']['id'];
        $account   = Account::findOrFail($accountId);

        $disable($account);

        return redirect()->back()->with('success', __('general.2fa.disabled'));
    }
}
