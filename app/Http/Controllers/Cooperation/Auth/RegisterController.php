<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Helpers\RegistrationHelper;
use App\Http\Controllers\Controller;
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
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    /*protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'postal_code' => ['required', new PostalCode()],
            'number' => ['required', new HouseNumber()],
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone_number' => [ 'nullable', new PhoneNumber() ],
        ]);
    }*/

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

        $address = $this->getAddressData($data['postal_code'], $data['number'], $data['addressid']);
        $data['bag_addressid'] = isset($address['bag_adresid']) ? $address['bag_adresid'] : '';

        $features = new BuildingFeature([
            'surface' => array_key_exists('adresopp', $address) ? $address['adresopp'] : null,
            'build_year' => array_key_exists('bouwjaar', $address) ? $address['bouwjaar'] : null,
        ]);

        $address = new Building($data);
        $address->user()->associate($user)->save();

        $features->building()->associate($address)->save();

        $cooperationId = \Session::get('cooperation');
        $cooperation = Cooperation::find($cooperationId);
        $user->cooperations()->attach($cooperation);

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
            return redirect()
                ->route('register')
                ->withErrors($validator);
        }

        $email = $request->get('u');
        $token = $request->get('t');

        $user = User::where('email', $email)->where('confirm_token', $token)->first();
        if (! $user instanceof User) {
            return redirect('register')->withErrors(trans('auth.confirm.error'));
        } else {
            $user->confirm_token = null;
            $user->save();

            // give the user the role resident
            $residentRole = Role::findByName('resident');
            $user->roles()->attach($residentRole);

            return redirect()->route('cooperation.login', ['cooperation' => \App::make('Cooperation')])->with('success', trans('auth.confirm.success'));
        }
    }

    /**
     * Check if a email already exists in the user table, and if it exist check if the user is registering on the wrong cooperation
     *
     * @param Cooperation $cooperation
     * @param Request $request
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
     * Connect the existing email to a cooperation
     *
     * @param Cooperation $cooperation
     * @param Request $request
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

    public function fillAddress(Request $request)
    {
        $postalCode = trim(strip_tags($request->get('postal_code', '')));
        $number = trim(strip_tags($request->get('number', '')));
        $extension = trim(strip_tags($request->get('house_number_extension', '')));

        $options = $this->getAddressData($postalCode, $number);

        $result = [];
        $dist = null;
        if (is_array($options) && count($options) > 0) {
            foreach ($options as $option) {
                $houseNumberExtension = (! empty($option['huisnrtoev']) && 'None' != $option['huisnrtoev']) ? $option['huisnrtoev'] : '';

                $newDist = null;
                if (! empty($houseNumberExtension) && ! empty($extension)) {
                    $newDist = levenshtein(strtolower($houseNumberExtension), strtolower($extension), 1, 10, 1);
                }
                if ((is_null($dist) || isset($newDist) && $newDist < $dist) && is_array($option)) {
                    // best match
                    $result = [
                        'id'                     => array_key_exists('bag_adresid', $option) ? md5($option['bag_adresid']) : '',
                        'street'                 => array_key_exists('straat', $option) ? $option['straat'] : '',
                        'number'                 => array_key_exists('huisnummer', $option) ? $option['huisnummer'] : '',
                        'house_number_extension' => $houseNumberExtension,
                        'city'                   => array_key_exists('woonplaats', $option) ? $option['woonplaats'] : '',
                    ];
                    $dist = $newDist;
                }
            }
        }

        return response()->json($result);
    }

    public function formResendConfirmMail(){
		return view('cooperation.auth.resend-confirm-mail');
    }

    public function resendConfirmMail(ResendConfirmMailRequest $request){
    	$validated = $request->validated();

    	$user = User::where('email', '=', $validated['email'])->whereNotNull('confirm_token')->first();

    	if (!$user instanceof User){
		    return redirect()->route('cooperation.auth.resend-confirm-mail', ['cooperation' => \App::make('Cooperation')])->with('error', trans('auth.confirm.success'));
	    }

    	SendRequestAccountConfirmationEmail::dispatch($user);

	    return redirect()->route('cooperation.auth.resend-confirm-mail', ['cooperation' => \App::make('Cooperation')])->with('success', trans('auth.confirm.success'));
    }

    protected function getAddressData($postalCode, $number, $pointer = null)
    {
        \Log::debug($postalCode.' '.$number.' '.$pointer);
        /** @var PicoClient $pico */
        $pico = app()->make('pico');
        $postalCode = str_replace(' ', '', trim($postalCode));
        $response = $pico->bag_adres_pchnr(['query' => ['pc' => $postalCode, 'hnr' => $number]]);

        if (! is_null($pointer)) {
            foreach ($response as $addrInfo) {
                if (array_key_exists('bag_adresid', $addrInfo) && $pointer == md5($addrInfo['bag_adresid'])) {
                    //$data['bag_addressid'] = $addrInfo['bag_adresid'];
                    \Log::debug(json_encode($addrInfo));

                    return $addrInfo;
                }
            }

            return [];
        }

        return $response;
    }
}
