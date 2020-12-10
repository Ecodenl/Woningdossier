<?php

return [
    'request-types' => [
        \App\Services\PrivateMessageService::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
        \App\Services\PrivateMessageService::REQUEST_TYPE_MEASURE => 'Anders...',
    ],

    'index' => [
        'header' => 'Actie ondernemen',
        'request-coach-conversation' => 'Coachgesprek aanvragen',

        'form' => [
            'title' => 'Contact over :measure_application_name',
            'are-you-sure' => 'Weet u zeker dat u de Coöperatie geen toegang wilt geven tot uw dossier?',
            'message' => 'Nadere toelichting op uw vraag',
            'submit' => 'Opsturen <span class="glyphicon glyphicon-envelope"></span>',

            'selected-option' => 'Waar kunnen we u bij helpen?:',
        ],
    ],

    'store' => [
        'success' => [
            \App\Models\InputSource::RESIDENT_SHORT => 'Uw aanvraag is sucessvol verstuurd, u krijgt zo spoedig mogelijk antwoord. u kunt <strong><a href=":url"">hier uw berichten bekijken</a> </strong> ',
            \App\Models\InputSource::COACH_SHORT => 'De aanvraag is aangemaakt voor de bewoner.',
        ],
    ],
];
