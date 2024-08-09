<?php

use App\Helpers\MediaHelper;

return [
    'index' => [
        'title' => 'Instellingen voor coöperatie',
        MediaHelper::LOGO => 'Selecteer hier een logo',
        MediaHelper::BACKGROUND => 'Selecteer hier een achtergrond afbeelding',
        MediaHelper::PDF_BACKGROUND => 'Selecteer hier een PDF achtergrond afbeelding',
        'current' => 'Huidig:',
    ],
    'store' => [
        'success' => 'Instellingen bijgewerkt',
    ],

    'form' => [
        'register-url' => [
            'label' => 'Registratie URL',
            'help' => '',
            'placeholder' => 'https://example.com/register',
        ],
        'verification-email-text' => [
            'label' => 'Verificatie e-mail tekst',
            'help' => 'Gebruik de volgende termen voor de volgende waarden: <ul><li>":verify_url" voor de verifieerlink <span class="glyphicon glyphicon-info-sign"></span></li><li>":first_name" voor voornaam</li><li>":last_name" voor achternaam</li><li>":hoomdossier_link" voor een link naar het startscherm</li><li>":cooperation_link" voor een link naar de cooperatie e-mail (of als niet ingesteld, de website)</li></ul>Voorbeeld: "Welkom :first_name bij het Hoomdossier" wordt "Welkom Harry bij het Hoomdossier". <br><br><i><span class="glyphicon glyphicon-info-sign"></span> Indien deze waarde niet is gegeven, wordt de verifieerlink automatisch <b>onderaan</b> toegevoegd.</i>',
            // NOTE: Formatting is important, as placeholder preserves white space!
            'placeholder' => 'Beste :first_name :last_name, 

U heeft een account aangevraagd op :hoomdossier_link

:verify_link

Als je hier vragen over hebt, kan je contact opnemen met :cooperation_link

Met vriendelijke groet,
Woondossier support',
        ],
    ],
];
