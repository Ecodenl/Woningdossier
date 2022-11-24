<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Models\Account;
use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Laravel\Fortify\Fortify;

class PasswordResetLinkController extends \Laravel\Fortify\Http\Controllers\PasswordResetLinkController
{
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(Request $request): Responsable
    {
        $request->validate([Fortify::email() => 'required|email']);

        $account = Account::whereEmail($request->input(Fortify::email()))->first();

        $valid = false;
        if ($account instanceof Account) {
            // If there is no user, then the account exists but not for this cooperation. We can't allow that.
            if ($account->user() instanceof User) {
                $valid = true;
            }
        }

        if ($valid) {
            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $status = $this->broker()->sendResetLink(
                $request->only(Fortify::email())
            );
        } else {
            $status = Password::INVALID_USER;
        }

        return $status == Password::RESET_LINK_SENT
                    ? app(SuccessfulPasswordResetLinkRequestResponse::class, ['status' => $status])
                    : app(FailedPasswordResetLinkRequestResponse::class, ['status' => $status]);
    }
}
