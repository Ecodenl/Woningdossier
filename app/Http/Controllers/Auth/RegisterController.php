<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\RegistrationHelper;
use App\Http\Requests\RegisterFormRequest;
use App\Models\Address;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Rules\HouseNumber;
use App\Rules\PhoneNumber;
use App\Rules\PostalCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
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
	 * @param  RegisterFormRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function register(RegisterFormRequest $request)
	{
		//$this->validator($request->all())->validate();

		event(new Registered($user = $this->create($request->all())));

		//$this->guard()->login($user);

		return $this->registered($request, $user)
			?: redirect($this->redirectPath())->with('success', trans('auth.register.form.message.success'));
	}

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
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

    	$address = new Address($data);
    	$address->user()->associate($user)->save();

    	return $user;
    }

	public function confirm(Request $request){
		$validator = \Validator::make( $request->all(), [
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
		if (!$user instanceof User){
			return redirect('register')->withErrors(trans('auth.confirm.error'));
		}
		else {
			$user->confirm_token = null;
			$user->save();
			return redirect()->route('login')->with('success', trans('auth.confirm.success'));
		}
	}
}
