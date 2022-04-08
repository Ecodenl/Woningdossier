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
            'email'                => 'Email address',
            'first-name'            => 'First name',
            'last-name'             => 'Last name',
            'password'              => 'Password',
            'password-confirmation' => 'Password (confirm)',
            'new-password'          => 'New password',
            'new-password_confirmation' => 'New password (confirm)',
            'postal-code'           => 'Postal code',
            'number'                => 'House number',
            'house-number-extension' => 'Extension',
            'street'                => 'Street',
            'city'                  => 'City',
            'phone-number'          => 'Phone number',
            'button'                => 'Register',
            'message'               => [
                'success'           => 'Thank you. We have sent you an e-mail with a confirmation link to complete your registration.',
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
