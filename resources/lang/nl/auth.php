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

    'failed'   => 'Uw gebruikersnaam of wachtwoord is onjuist.',
    'cooperation' => 'U bent geen lid van deze coöperatie.',
    'throttle' => 'Te veel login pogingen. U kunt het opniew proberen over :seconds seconde(n).',

    'register' => [
        'form' => [
            'connected-address'     => 'Adres:',
            'header'                => 'Registreren',
            'e-mail'                => 'E-mailadres',
            'first_name'            => 'Voornaam',
            'last_name'             => 'Achternaam',
            'password'              => 'Wachtwoord',
            'password_confirmation' => 'Wachtwoord (bevestigen)',
            'current_password'      => 'Huidig wachtwoord',
            'new_password'          => 'Nieuw wachtwoord',
            'new_password_confirmation' => 'Nieuw wachtwoord (bevestigen)',
            'postal_code'           => 'Postcode',
            'number'                => 'Huisnummer',
            'house_number_extension' => 'Toevoeging',
            'street'                => 'Straat',
            'city'                  => 'Plaats',
            'phone_number'          => 'Telefoonnummer',
            'button'                => 'Registreren',
            'message'               => [
                'success'           => 'Bedankt. We hebben een e-mail met een bevestigingslink naar u toegestuurd om uw registratie te voltooien.',
            ],
        ],
    ],
    'login' => [
        'form' => [
            'header'                => 'Inloggen',
            'e-mail'                => 'E-mailadres',
            'password'              => 'Wachtwoord',
            'remember_me'           => 'Onthouden',
            'button'                => 'Login',
            'forgot_password'       => 'Wachtwoord vergeten?',
        ],
    ],

    'general-data' => [
        'may-not-be-filled' => 'Dit veld mag niet gevuld zijn onder deze omstandigheden',
    ],
    'confirm' => [
        'success' => 'Uw account is bevestigd. U kunt nu inloggen met uw gebruikersnaam en wachtwoord.',
        'error' => 'Uw bevestigingslink is ongeldig. Is uw account wellicht al bevestigd?',
    ],
];
