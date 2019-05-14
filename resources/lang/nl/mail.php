<?php

return [
    'account-created-by-cooperation' => [
        'subject' => 'Welkom in het Hoomdossier',
        'salutation' => 'Beste :first_name :last_name,',
        'text' => 'Er is een account voor u aangemaakt op :hoomdossier_link<br><br>Bevestig uw account door onderstaande link te volgen:<br><br>:confirm_url<br><br>Als u hierover vragen hebt, kunt u contact opnemen met :cooperation_link',
        'kind_regards' => 'Met vriendelijke groet, <br>:app_name support'
    ],

    'changed-email' => [
        'subject' => 'Hoomdossier: uw e-mail adres is aangepast.',
        'salutation' => 'Beste :first_name :last_name,',
        'text' => 'Het e-mailadres voor uw Hoomdossier is aangepast. Indien dit gewenst was hoeft u niets te doen. Als u deze activiteit niet herkent kunt u dit ongedaan maken door de onderstaande link te volgen. Wij raden u aan om vervolgens uw wachtwoord te resetten.<br><br>:recover_old_email_url<br><br>Als u hierover vragen hebt, kunt u contact opnemen met :cooperation_link',
        'kind_regards' => 'Met vriendelijke groet, <br>:app_name support'
    ],

    'confirm-account' => [
        'subject' => 'Welkom in het Hoomdossier',
        'salutation' => 'Beste :first_name :last_name,',
        'text' => 'U heeft een account aangevraagd op :hoomdossier_link<br><br>Bevestig uw account door onderstaande link te volgen:<br><br>:confirm_url<br><br>Als u hierover vragen hebt, kunt u contact opnemen met :cooperation_link',
        'kind_regards' => 'Met vriendelijke groet, <br>:app_name support'
    ],

    'reset_password' => [
        'why' => 'U ontvangt deze mail omdat iemand een wachtwoord reset heeft aangevraagd voor uw account.',
        'action' => 'Wachtwoord resetten',
        'not_requested' => 'Als u geen wachtwoord reset heeft aangevraagd hoeft u geen actie te ondernemen.',
    ],

    'unread-message-count' => [
        'subject' => 'U heeft :unread_message_count ongelezen berichten',
        'salutation' => 'Beste :first_name :last_name,',
        'text' => 'Er zijn :unread_message_count ongelezen berichten voor u in het Hoomdossier.',
        'kind_regards' => 'Met vriendelijke groet, <br>:app_name support'
    ],

];
