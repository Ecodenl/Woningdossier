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

    'session-invalid' => 'Uw sessie is verlopen, log opnieuw in.',
    'failed'   => 'Uw gebruikersnaam of wachtwoord is onjuist.',
    'cooperation' => 'U bent geen lid van deze coöperatie.',
    'throttle' => 'Te veel login pogingen. U kunt het opniew proberen over :seconds seconde(n).',
    'inactive' => 'U kunt nog niet inloggen omdat uw account nog niet is bevestigd. U kunt uw account bevestigen via de bevestigingslink in de eerder gestuurde e-mail. <a href=":resend-link">Niet ontvangen?</a>',


    'register' => [
        'form' => [
            'connected-address'     => 'Adres:',
            'header'                => 'Registreren',
            'e-mail'                => 'E-mailadres',
            'e-mail-exists'         => 'Het e-mailadres is al geregistreerd, wij hebben nog een aantal gegevens van uw nodig. U kunt hierna inloggen met uw E-mailadres en huidige wachtwoord.',
            'already-member'        => 'U bent a lid van deze coöperatie, ga naar de <strong><a href='.url('login').'>Login pagina</a></strong>',
            'connect'               => 'Koppelen aan deze coöperatie',
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
                'account-connected' => 'Het account is gekoppeld, u kunt nu inloggen met het wachtwoord waar u mee inlogde bij uw vorige coöperatie.',
            ],

            'possible-wrong-email' => 'Het lijkt er op dat er een fout in het e-mailadres zit, weet je zeker dat het opgegeven e-mailadres juist is ?',
            'possible-wrong-postal-code' => 'De postcode die is opgegeven lijkt fout te zijn, weet u zeker dat deze correct is ingevuld ? Als u hiervan zeker bent kunt u doorgaan.'
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
        'warning' => 'Er is geen woning gekoppeld aan uw account, om het Hoomdossier goed te gebruiken hebben wij uw adres nodig.',
    ],

    'general-data' => [
        'may-not-be-filled' => 'Dit veld mag niet gevuld zijn onder deze omstandigheden',
    ],
    'email' => [
        'form' => [
            'header' => 'Wachtwoord resetten',
            'send-reset-link' => 'Verstuur reset link',
        ],
    ],
    'reset' => [
        'form' => [
            'header' => 'Wachtwoord instellen',
            'set-password' => 'Nieuw wachtwoord instellen',
        ],
        'inactive' => 'Uw wachtwoord is gereset, maar uw account is nog niet bevestigd. U kunt uw account bevestigen via de bevestigingslink in de eerder gestuurde e-mail.',
        'success' => 'Uw wachtwoord is gereset, u kunt nu inloggen.'
    ],
    'confirm' => [
        'success' => 'Uw account is bevestigd. U kunt nu inloggen met uw gebruikersnaam en wachtwoord.',
        'error' => 'Uw bevestigingslink is ongeldig. Is uw account wellicht al bevestigd?',
        'email-error' => 'We konden uw e-mailadres niet vinden. Weet u zeker dat u eerder geregistreerd bent?',
        'email-success' => 'De bevestigingslink is opnieuw naar u verstuurd.',
    ],
];
