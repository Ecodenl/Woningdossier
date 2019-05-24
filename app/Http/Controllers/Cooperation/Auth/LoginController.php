<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        return array_merge(
            $request->only($this->username(), 'password'),
            ['active' => 1, 'confirm_token' => null]
        );
    }

    /**
     * Handle a login request to the application.
     *
     * @param  Request  $request
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response|void
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->guard()->validate($this->credentials($request))) {
            /** @var User $user */
            $user = $this->guard()->getLastAttempted();

            if (! $user->isAssociatedWith(\App::make('Cooperation'))) {
                throw ValidationException::withMessages([
                    'cooperation' => [trans('auth.cooperation')],
                ]);
            }
        } else {
            // So it wasn't alright. Check if it was because of the confirm_token
            $userEmail = $request->get('email');
            $isPending = User::where('email', '=', $userEmail)->whereNotNull('confirm_token')->count() > 0;
            if ($isPending) {
                \Log::debug("The user tried to log in, but isn't confirmed yet.");
                throw ValidationException::withMessages([
                    'confirm_token' => [__('auth.inactive', ['resend-link' => route('cooperation.auth.form-resend-confirm-mail')])],
                ]);
            }
        }


        // try to login the user with the given credentials from the request.
        if ($this->attemptLogin($request)) {

            $user = \Auth::user();

            // getUrlByRoleName expects spatie model.
            $role = \Spatie\Permission\Models\Role::findByName($user->roles()->first()->name);

            $user->roles->count() == 1 ? $this->redirectTo = RoleHelper::getUrlByRole($role) : $this->redirectTo = '/admin';

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

}
