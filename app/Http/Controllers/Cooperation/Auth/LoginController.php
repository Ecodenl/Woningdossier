<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\InputSource;
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
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
        }

		if ($this->attemptLogin($request)) {
		    $user = \Auth::user();

		    // get the first building from the user
            $building = $user->buildings()->first();

            // we cant query on the Spatie\Role model so we first get the result on the "original model"
            $role = Role::findByName($user->roles->first()->name);

            // get the input source
            $inputSource = $role->inputSource;

            // if there is only one role set for the user, and that role does not have an input source we will set it to resident.
            if (!$role->inputSource instanceof InputSource) {
                $inputSource = InputSource::findByShort('resident');
            }

            // set the required sessions
            HoomdossierSession::setHoomdossierSessions($building, $inputSource, $inputSource, $role);

            // set the redirect url
            if ($user->roles->count() == 1) {
			    $this->redirectTo = RoleHelper::getUrlByRole( $role );
            }
			else {
                $this->redirectTo = '/admin';
			}


			return $this->sendLoginResponse($request);
		}

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);

		return $this->sendFailedLoginResponse($request);
	}

    /**
     * Send the response after the user was authenticated.
     *
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /*protected function sendLoginResponse ($request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        //return $this->authenticated($request, $this->guard()->user()) ? : redirect()->route('cooperation.home');
	    return $this->authenticated($request, $this->guard()->user()) ? : redirect($this->redirectTo);
    }*/
}
