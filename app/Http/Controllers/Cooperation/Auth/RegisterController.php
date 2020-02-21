<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Events\Registered;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\HoomdossierSession;
use App\Helpers\PicoHelper;
use App\Helpers\RegistrationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;
use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\Role;
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
     * @param RegisterFormRequest $request
     * @param Cooperation $cooperation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(RegisterFormRequest $request, Cooperation $cooperation)
    {
        $user = UserService::register($cooperation, ['resident'], $request->all());
        $account = $user->account;

        if ($account->wasRecentlyCreated) {
            $successMessage = __('auth.register.form.message.success');
            \Event::dispatch(new Registered($cooperation, $user));
        } else {
            $successMessage = __('auth.register.form.message.account-connected');
            UserAssociatedWithOtherCooperation::dispatch($cooperation, $user);
        }

        return redirect($this->redirectPath())->with('success', $successMessage);
    }



    /**
     * Check if a email already exists in the user table, and if it exist check if the user is registering on the wrong cooperation.
     *
     * @param Cooperation $cooperation
     * @param Request $request
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
