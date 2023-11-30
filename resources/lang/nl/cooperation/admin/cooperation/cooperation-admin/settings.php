<?php

use App\Helpers\MediaHelper;

return [
    'index' => [
        'title' => 'Instellingen voor coöperatie',
        MediaHelper::LOGO => 'Selecteer hier een logo',
        MediaHelper::BACKGROUND => 'Selectier hier een achtergrond afbeelding',
        MediaHelper::PDF_BACKGROUND => 'Selectier hier een PDF achtergrond afbeelding',
        'current' => 'Huidig:',
    ],
    'store' => [
        'success' => 'Instellingen bijgewerkt',
    ],

    'form' => [
        'register-url' => [
            'label' => 'Registratie URL',
            'placeholder' => 'https://example.com/register',
        ],
    ],
];