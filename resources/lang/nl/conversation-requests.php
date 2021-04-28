<?php

return [
    'request-types' => [
        \App\Services\PrivateMessageService::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
        \App\Services\PrivateMessageService::REQUEST_TYPE_MEASURE => 'Anders...',
    ],

    'index' => [
        'header' => 'Actie ondernemen',
        'request-coach-conversation' => 'Coachgesprek aanvragen',

        'text' => 'De gegevens worden uitsluitend door de coöperatie gebruikt om u in uw bewonersreis te ondersteunen. Uw persoonlijke gegevens worden niet doorgegeven aan derden. Meer informatie over de verwerking van uw data en wat we ermee doen kunt u vinden in ons privacy statement.',
        'form' => [
            'title' => 'Contact over :measure_application_name',
            'are-you-sure' => 'Weet u zeker dat u de Coöperatie geen toegang wilt geven tot uw dossier?',
            'message' => 'Nadere toelichting op uw vraag',
            'submit' => 'Opsturen <span class="glyphicon glyphicon-envelope"></span>',
            'allow_access' => 'Ik geef toestemming aan :cooperation om de gegevens uit mijn Hoomdossier in te zien en in overleg met mij deze gegevens aan te passen.',

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
