<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Cooperation;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Reset the given user's password.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // Sorry.. creating a complete custom password broker service provider
        // (https://stackoverflow.com/questions/40532296/laravel-5-3-password-broker-customization)
        // seems a LOT more overkill than this..
        $userEmail = $request->get('email');

        $isPending = Account::where('email', '=', $userEmail)->whereNotNull('confirm_token')->count() > 0;
        if ($isPending) {
            \Log::debug('The user has resetted his password, but has not confirmed his account. Redirecting to login page with a message..');

            return redirect(route('cooperation.auth.login'))->with('warning', __('auth.reset.inactive'));
        }

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return Password::PASSWORD_RESET == $response
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($request, $response);
    }

    public function sendResetFailedResponse($request, $response)
    {
        return redirect()->back()
            ->withInput($request->only('email'))
            ->with('token_invalid', __($response, ['password_request_link' => route('cooperation.auth.password.request.index')]));
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param $response
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetResponse($response)
    {
        // same as for login controller: redirect to appropriate page
        // the guard()->user() will return the auth model, in our case this is the Account model
        // but we want the user from the account, so thats why we do ->user()->user();
        $user = $this->guard()->user()->user();

        $role = Role::findByName($user->roles()->first()->name);

        1 == $user->roles->count() ? $this->redirectTo = RoleHelper::getUrlByRole($role) : $this->redirectTo = '/home';

        return redirect($this->redirectPath())->with('status', trans($response));
    }

    protected function resetPassword(Account $account, $password)
    {
        $account->password = \Hash::make($password);

        $account->setRememberToken(Str::random(60));

        $account->save();

        event(new PasswordReset($account));

        $this->guard()->login($account);
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param $token
     * @param $email
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show(Request $request, Cooperation $cooperation, $token, $email)
    {
        return $this->emailEncryptionIsValid($email) ? $this->showPasswordResetForm($request, $token, $email) : $this->sendBackHome();
    }

    public function sendBackHome()
    {
        return redirect(route('cooperation.welcome'));
    }

    /**
     * Method to display the reset form.
     *
     * @param $token
     * @param $email
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPasswordResetForm(Request $request, $token, $email)
    {
        $email = decrypt($email);

        // here we will make up the credentials, the broker needs credentials to return a proper response.
        // we only need the broker for the validateReset method, but its protected and creating a custom broker seems overkill to me.
        $password = str_random(6);
        $credentials = [
            'token' => $token,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        // so, check if the token and email are valid.
        $validationResponse = $this->broker()->validateReset($credentials);

        if (PasswordBroker::INVALID_TOKEN == $validationResponse) {
            $request->session()->flash('token_invalid', __($validationResponse, ['password_request_link' => route('cooperation.auth.password.request.index')]));
        }

        return view('cooperation.auth.passwords.reset.show', compact('token', 'email', 'response'));
    }

    /**
     * Check whether the email encryption is valid.
     *
     * @param $encryption
     *
     * @return bool
     */
    public function emailEncryptionIsValid($encryption)
    {
        try {
            decrypt($encryption);
        } catch (DecryptException $decryptException) {
            return false;
        }

        return true;
    }
}
