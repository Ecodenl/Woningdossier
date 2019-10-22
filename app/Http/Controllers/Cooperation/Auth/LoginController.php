<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('cooperation.auth.login');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        // destroy all HoomdossierSessions

        HoomdossierSession::destroy();

        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), ['active' => 1, 'confirm_token' => null]);
    }


    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @param Cooperation $cooperation
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws ValidationException
     */
    public function login(Request $request, Cooperation $cooperation)
    {
        $this->validateLogin($request);
        $user = null;

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // validate the credentials from the user
        if ($this->guard()->validate($this->credentials($request))) {
            /** @var Account $account */
            $account = $this->guard()->getLastAttempted();

            if (! $account->isAssociatedWith($cooperation)) {
                throw ValidationException::withMessages([
                    'cooperation' => [trans('auth.cooperation')],
                ]);
            }

            if (!$account->user()->building instanceof Building) {
                Log::error('no building attached for user id: '.$account->user()->id.' account id:' .$account->id);
                return redirect(route('cooperation.create-building.index'))->with('warning', __('auth.login.warning'));
            }
        }

        // check if the account is confirmed.
        if ($this->accountIsNotConfirmed($request->get('email'))) {
            $this->sendAccountNotConfirmedResponse();
        }


        // everything is ok with the user at this point, now we log him in.
        if ($this->attemptLogin($request)) {
            // the guard()->user() will return the auth model, in our case this is the Account model
            // but we want the user from the account, so thats why we do ->user()->user();
            $user = $this->guard()->user()->user();
            $role = Role::findByName($user->roles()->first()->name);

            1 == $user->roles->count() ? $this->redirectTo = RoleHelper::getUrlByRole($role) : $this->redirectTo = '/admin';

            return $this->sendLoginResponse($request);
        }

        // if the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
//        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Check if a account is confirmed based on its email address.
     *
     * @param $email
     *
     * @return bool
     */
    private function accountIsNotConfirmed($email): bool
    {
        // So it wasn't alright. Check if it was because of the confirm_token
        $isPending = Account::where('email', '=', $email)->whereNotNull('confirm_token')->count() > 0;

        return $isPending;
    }

    /**
     * Send account not confirmed response.
     */
    private function sendAccountNotConfirmedResponse()
    {
        // throw validation exception, with a confirmation resend link.
        throw ValidationException::withMessages([
            'confirm_token' => [
                __('auth.inactive', ['resend-link' => route('cooperation.auth.confirm.resend.show')]),
            ],
        ]);
    }
}
