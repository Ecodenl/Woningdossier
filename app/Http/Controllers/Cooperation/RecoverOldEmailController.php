<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\User;
use App\Http\Controllers\Controller;

class RecoverOldEmailController extends Controller
{
    /**
     * Method to recover the old email adres based on the old_email_token
     *
     * @param  Cooperation  $cooperation
     * @param $token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recover(Cooperation $cooperation, $token)
    {
        if (\Auth::check()) {
            HoomdossierSession::destroy();
            \Auth::logout();
        }
        // get the user that wants his email to get recovered
        $user = User::where('old_email_token', $token)->first();

        if ($user instanceof User) {

            // recover the old email address and set he old stuff to null.
            $user->update([
                'email'           => $user->old_email,
                'old_email'       => null,
                'old_email_token' => null
            ]);

            // generate a token and create a row in the password_resets
            $token = app('auth.password.broker')->createToken($user);

            // send the user a notification in case he leaves the page.
            $user->sendPasswordResetNotification($token);

            // redirect them to the password reset
            return redirect()
                ->route('cooperation.password.reset', ['token' => $token, 'cooperation' => $cooperation->slug])
                ->with('success', __('recover-old-email.recover.success'));
        } else {

            return redirect()
                ->route('cooperation.login')
                ->with('warning', __('recover-old-email.recover.warning'));
        }
    }
}
