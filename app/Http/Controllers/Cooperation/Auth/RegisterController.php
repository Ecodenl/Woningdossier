<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Helpers\PicoHelper;
use App\Helpers\RegistrationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\FillAddressRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Http\Requests\ResendConfirmMailRequest;
use App\Jobs\SendRequestAccountConfirmationEmail;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\Role;
use App\Models\User;
use App\Rules\HouseNumber;
use App\Rules\PhoneNumber;
use App\Rules\PostalCode;
use Ecodenl\PicoWrapper\PicoClient;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    public function register(RegisterFormRequest $request)
    {
        event(new Registered($user = $this->create($request->all())));

        return redirect($this->redirectPath())->with('success', __('auth.register.form.message.success'));
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone_number' => is_null($data['phone_number']) ? '' : $data['phone_number'],
            'confirm_token' => RegistrationHelper::generateConfirmToken(),
        ]);

        // now get the pico address data.
        $picoAddressData = PicoHelper::getAddressData(
            $data['postal_code'], $data['number']
        );

        $data['bag_addressid'] = $picoAddressData['id'] ?? $data['addressid'];

        $features = new BuildingFeature([
            'surface' => $picoAddressData['surface'] ?? null,
            'build_year' => $picoAddressData['build_year'] ?? null,
        ]);

        $address = new Building($data);
        $address->user()->associate($user)->save();

        $features->building()->associate($address)->save();

        $cooperationId = \Session::get('cooperation');
        $cooperation = Cooperation::find($cooperationId);
        $user->cooperations()->attach($cooperation);

        $residentRole = Role::findByName('resident');
        $user->roles()->attach($residentRole);

        return $user;
    }

    public function confirm(Request $request)
    {
        $validator = \Validator::make($request->all(), [
                'u' => 'required|email',
                't' => 'required|alpha_num',
            ]
        );

        if ($validator->fails()) {
            return redirect(route('cooperation.home'));
        }

        $email = $request->get('u');
        $token = $request->get('t');

        $user = User::where('email', $email)->where('confirm_token', $token)->first();
        if (! $user instanceof User) {
            return redirect('register')->withErrors(trans('auth.confirm.error'));
        } else {
            $user->confirm_token = null;
            $user->save();

            if (0 == $user->roles()->count()) {
                \Log::debug("A user confirmed his account and there was no role set, the id = {$user->id} we set the role to resident so no exception");

                $residentRole = Role::findByName('resident');
                $user->roles()->attach($residentRole);
            }

            return redirect()->route('cooperation.login', ['cooperation' => \App::make('Cooperation')])->with('success', trans('auth.confirm.success'));
        }
    }

    /**
     * Check if a email already exists in the user table, and if it exist check if the user is registering on the wrong cooperation.
     *
     * @param Cooperation $cooperation
     * @param Request     $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkExistingEmail(Cooperation $cooperation, Request $request)
    {
        $email = $request->get('email');
        $user = User::where('email', $email)->first();

        $response = ['email_exists' => false, 'user_is_already_member_of_cooperation' => false];

        if ($user instanceof User) {
            $response['email_exists'] = true;

            // check if the is already attached
            if ($user->cooperations->contains($cooperation)) {
                $response['user_is_already_member_of_cooperation'] = true;
            }

            return response()->json($response);
        } else {
            return response()->json($response);
        }
    }

    /**
     * Connect the existing email to a cooperation.
     *
     * @param Cooperation $cooperation
     * @param Request     $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function connectExistingAccount(Cooperation $cooperation, Request $request)
    {
        $email = $request->get('existing_email');
        $user = User::where('email', $email)->first();

        // okay, the user does exists
        if ($user instanceof User) {
            // check if the is already attached
            if ($user->cooperations->contains($cooperation)) {
                return redirect()->back();
            }

            $cooperation->users()->attach($user);

            return redirect(url('login'))->with('account_connected', __('auth.register.form.message.account-connected'));
        }

        // user is playing, redirect them back
        return redirect()->back();
    }



    public function formResendConfirmMail()
    {
        return view('cooperation.auth.resend-confirm-mail');
    }

    public function resendConfirmMail(Cooperation $cooperation, ResendConfirmMailRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', '=', $validated['email'])->whereNotNull('confirm_token')->first();

        if (! $user instanceof User) {
            return redirect()->route('cooperation.auth.resend-confirm-mail', ['cooperation' => $cooperation])
                             ->withInput()
                             ->withErrors(['email' => trans('auth.confirm.email-error')]);
        }

        SendRequestAccountConfirmationEmail::dispatch($user, $cooperation);

        return redirect()->route('cooperation.auth.resend-confirm-mail', ['cooperation' => $cooperation])->with('success', trans('auth.confirm.email-success'));
    }


}
