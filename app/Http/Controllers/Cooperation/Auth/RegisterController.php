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
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function register(RegisterFormRequest $request, Cooperation $cooperation)
    {
        // try to obtain the existing account
        $account = Account::where('email', $request->get('email'))->first();

        // if its not found we will create a new one.
        if (!$account instanceof Account) {
            $account = $this->createNewAccount($request->only('email', 'password'));
        }

        $user = $this->createNewUser($account, $request->except('email', 'password'));

        // associate it with the user
        $user->account()->associate(
            $account
        )->save();

        if ($account->wasRecentlyCreated) {
            $successMessage = __('auth.register.form.message.success');
            \Event::dispatch(new Registered($cooperation, $user));
        } else {
            $successMessage = __('auth.register.form.message.account-connected');
            \Event::dispatch(new UserAssociatedWithOtherCooperation($cooperation, $user));
        }

        return redirect($this->redirectPath())->with('success', $successMessage);
    }

    /**
     * Create a new account.
     *
     * @param array $data
     *
     * @return Account
     */
    private function createNewAccount(array $data): Account
    {
        return Account::create([
            'email' => $data['email'],
            'password' => \Hash::make($data['password']),
            'confirm_token' => RegistrationHelper::generateConfirmToken(),
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param $account
     * @param array $data
     * @return User
     */
    private function createNewUser($account, array $data): User
    {

        \Illuminate\Support\Facades\Log::debug('account id for registration: '.$account->id);
        // Create the user for an account
        $user = User::create(
            [
                'account_id' => $account->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone_number' => is_null($data['phone_number']) ? '' : $data['phone_number'],
            ]
        );

        // now get the picoaddress data.
        $picoAddressData = PicoHelper::getAddressData(
            $data['postal_code'], $data['number']
        );

        $data['bag_addressid'] = $picoAddressData['id'] ?? $data['addressid'] ?? '';
        $data['extension'] = $data['house_number_extension'] ?? null;

        $features = new BuildingFeature([
            'surface' => empty($picoAddressData['surface']) ? null : $picoAddressData['surface'],
            'build_year' => empty($picoAddressData['build_year']) ? null : $picoAddressData['build_year'],
        ]);

        // create the building for the user
        $building = Building::create($data);

        $cooperation = HoomdossierSession::getCooperation(true);
        $residentRole = Role::findByName('resident');

        // associate multiple models with each other
        $building->user()->associate(
            $user
        )->save();

        $features->building()->associate(
            $building
        )->save();

        $user->cooperation()->associate(
            $cooperation
        )->save();

        $user->assignRole($residentRole);
        // turn on when merged
        $building->setStatus('active');

        return $user;
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
