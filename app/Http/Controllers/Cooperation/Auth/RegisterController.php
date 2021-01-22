<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Events\Registered;
use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // middle ware on auth routes instead on controller
//        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('cooperation.auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(RegisterFormRequest $request, Cooperation $cooperation)
    {
        $user = UserService::register($cooperation, ['resident'], $request->all());
        $account = $user->account;

        if ($account->wasRecentlyCreated) {
            $account->sendEmailVerificationNotification();
            \Event::dispatch(new Registered($cooperation, $user));
        } else {
            UserAssociatedWithOtherCooperation::dispatch($cooperation, $user);
        }
        // at this point, a user cant register without accepting the privacy terms.
        UserAllowedAccessToHisBuilding::dispatch($user->building);

        $this->guard()->login($account);

        return $this->sendRegisteredResponse();
    }

    private function sendRegisteredResponse()
    {
        // the case for a user that connect itself to a other cooperation
        if ($this->guard()->user()->hasVerifiedEmail()) {
            return redirect(RoleHelper::getUrlByRole($this->guard()->user()->user()->roles()->first()))
                ->with('success', __('auth.register.form.message.account-connected'));
        }

        return redirect()
            ->route('cooperation.auth.verification.notice');
    }

    /**
     * Check if a email already exists in the user table, and if it exist check if the user is registering on the wrong cooperation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkExistingEmail(Cooperation $cooperation, Request $request)
    {
        $email = $request->get('email');
        $account = Account::where('email', $email)->first();

        $response = ['email_exists' => false, 'user_is_already_member_of_cooperation' => false];

        if ($account instanceof Account) {
            $response['email_exists'] = true;

            // check if the user is a member of the cooperation
            if ($account->user() instanceof User) {
                $response['user_is_already_member_of_cooperation'] = true;
            }

            return response()->json($response);
        }

        return response()->json($response);
    }
}
