<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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
    protected $redirectTo = '/home';

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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
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
        $isPending = User::where('email', '=', $userEmail)->whereNotNull('confirm_token')->count() > 0;
        if ($isPending) {
            //$this->guard()->logout();
            \Log::debug('The user has resetted his password, but has not confirmed his account. Redirecting to login page with a message..');

            return redirect(route('cooperation.login'))->with('warning', __('auth.reset.inactive'));
        }

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return Password::PASSWORD_RESET == $response
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($request, $response);
    }

    protected function resetPassword($user, $password)
    {
        $user->password = \Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        // only for confirmed users
        if (is_null($user->confirm_token)) {
            // get the first building from the user
            $building = $user->buildings()->first();

            // we cant query on the Spatie\Role model so we first get the result on the "original model"
            $role = Role::findByName($user->roles->first()->name);

            // get the input source
            $inputSource = $role->inputSource;

            // if there is only one role set for the user, and that role does not have an input source we will set it to resident.
            if (! $inputSource instanceof InputSource) {
                $inputSource = InputSource::findByShort('resident');
            }

            // set the required sessions
            HoomdossierSession::setHoomdossierSessions($building,
                $inputSource,
                $inputSource,
                $role);

            // set the redirect url
            if (1 == $user->roles->count()) {
            	// don't check the user as he's not logged in yet
                $this->redirectTo = RoleHelper::getUrlByRole($role, false);
            } else {
                $this->redirectTo = '/admin';
            }

            \Log::debug("Redirect to: " . $this->redirectTo);

            $this->guard()->login($user);
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $token
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, Cooperation $cooperation, $token = null)
    {
        return view('cooperation.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
