<?php

namespace App\Http\Controllers\Api;

use App\Events\Registered;
use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cooperation\RegisterFormRequest;
use App\Mail\UserCreatedEmail;
use App\Models\Account;
use App\Models\Cooperation;
use App\Services\UserService;

class RegisterController extends Controller
{
    public function store(RegisterFormRequest $request, Cooperation $cooperation)
    {
        $requestData = $request->all();

        // normally we would have a user given password, however we will reset the password right after its created.
        // this way the user can set his own password.
        $requestData['password'] = \Hash::make(Str::randomPassword());

        $user = UserService::register($cooperation, ['resident'], $requestData);
        $account = $user->account;

        // if the account is recently created we have to send a confirmation mail
        // else we send a notification to the user he is associated with a new cooperation
        if ($account->wasRecentlyCreated) {
            // and send the account confirmation mail.
            $this->sendAccountConfirmationMail($cooperation, $account);
            $account->markEmailAsVerified();
        } else {
            UserAssociatedWithOtherCooperation::dispatch($cooperation, $user);
        }

        // at this point, a user cant register without accepting the privacy terms.
        UserAllowedAccessToHisBuilding::dispatch($user->building);

        return response([], 200);
    }

    private function sendAccountConfirmationMail(Cooperation $cooperation, Account $account)
    {
        $token = app('auth.password.broker')->createToken($account);

        // send a mail to the user
        \Mail::to($account->email)->sendNow(new UserCreatedEmail($cooperation, $account->user(), $token));
    }
}
