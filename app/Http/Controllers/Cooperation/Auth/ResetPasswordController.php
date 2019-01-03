<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
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

    protected function resetPassword($user, $password)
    {
        $user->password = \Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

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

        $this->guard()->login($user);
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
