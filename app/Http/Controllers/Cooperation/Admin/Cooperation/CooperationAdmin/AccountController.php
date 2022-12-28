<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Events\ParticipantAddedEvent;
use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\Hoomdossier;
use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\AccountFormRequest;
use App\Http\Requests\Cooperation\Admin\Cooperation\UserFormRequest;
use App\Mail\UserCreatedEmail;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use App\Services\TwoFactorAuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Spatie\Permission\Models\Role;

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
