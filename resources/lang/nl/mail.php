<?php

return [
    'confirm_account1' => [
        'salutation' => 'Beste :first_name :last_name,',
        'text' => 'U heeft een account aangevraagd op :home_url. U kunt uq account bevestigen via de volgende link:<br><br>:confirm_url',
        'signature' => '<br>Met vriendelijke groet,<br>:app_name',
    ],

    'confirm_account' => [
        'salutation' => 'Beste :first_name :last_name,',
        'text' => '
        Er is een account voor u aangemaakt op http://:subdomain.hoomdossier.nl.
        <br><br>
        Bevestig uw account door onderstaande link te volgen:
        <br><br>
        :confirm_url
        <br>Als u hierover vragen hebt, kunt u contact opnemen met :cooperation_link 
    '],
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
