<?php

return [
    'confirm_account' => [
        'subject' => 'Welkom in het Hoomdossier',
        'salutation' => 'Beste :first_name :last_name,',
        'text' => 'Er is een account voor u aangemaakt op :hoomdossier_link<br><br>Bevestig uw account door onderstaande link te volgen:<br><br>:confirm_url<br><br>Als u hierover vragen hebt, kunt u contact opnemen met :cooperation_link',
        'kind_regards' => 'Met vriendelijke groet, <br>:app_name support'
    ],
    'reset_password' => [
        'why' => 'U ontvangt deze mail omdat iemand een wachtwoord reset heeft aangevraagd voor uw account.',
        'action' => 'Wachtwoord resetten',
        'not_requested' => 'Als u geen wachtwoord reset heeft aangevraagd hoeft u geen actie te ondernemen.',
    ],
    'user-created' => [
        'why' => 'U heeft deze e-mail ontvangen omdat er account voor u is aangemaakt',
        'action' => 'Aan de slag',
        'signature' => '<br>Met vriendelijke groet,<br>:app_name',
    ],
];
