<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'cooperation' => 'You are not a member of this cooperation.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'register' => [
	    'form' => [
		    'header'                => 'Registration',
		    'e-mail'                => 'Email address',
		    'first_name'            => 'First name',
		    'last_name'             => 'Last name',
		    'password'              => 'Password',
		    'password_confirmation' => 'Password (confirm)',
		    'current_password'      => 'Current password',
		    'new_password'          => 'New password',
		    'new_password_confirmation' => 'New password (confirm)',
		    'postal_code'           => 'Postal code',
		    'number'                => 'House number',
		    'house_number_extension' => 'Extension',
		    'street'                => 'Street',
		    'city'                  => 'City',
		    'phone_number'          => 'Phone number',
		    'button'                => 'Register',
		    'message'               => [
			    'success'           => 'Thank you. We have sent you an e-mail with a confirmation link to complete your registration.'	,
		    ],
	    ],
    ],
    'login' => [
	    'form' => [
		    'header'                => 'Login',
		    'e-mail'                => 'Email address',
		    'password'              => 'Password',
		    'remember_me'           => 'Remember me',
		    'button'                => 'Login',
		    'forgot_password'       => 'Forgot password?',
	    ],
    ],
    'confirm' => [
	    'success' => 'Your account is confirmed. You can now log in with your username and password.',
	    'error' => 'Your confirmation link is invalid. Perhaps your account is already confirmed?',
    ],
];
