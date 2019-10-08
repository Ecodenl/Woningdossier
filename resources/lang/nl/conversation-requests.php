<?php

return [

    'index' => [
        'header' => 'Actie ondernemen',
        'text' => 'De gegevens worden uitsluitend door de coöperatie gebruikt om u in uw bewonersreis te ondersteunen. Uw persoonlijke gegevens worden niet doorgegeven aan derden. Meer informatie over de verwerking van uw data en wat we ermee doen kunt u vinden in ons privacy statement.',

        'form' => [
            'no-measure-application-name-title' => 'Contact opnemen',
            'title' => 'Contact over :measure_application_name',
            'allow_access' => 'Ik geef toesteming aan :cooperation om de gegevens uit mijn Hoomdossier in te zien en in overleg met mij deze gegevens aan te passen.',
            'are-you-sure' => 'Weet u zeker dat u de Coöperatie geen toegang wilt geven tot uw dossier?',
            'action' => 'Actie',
            'message' => 'Nadere toelichting op uw vraag',
            'submit' => 'Opsturen <span class="glyphicon glyphicon-envelope"></span>',

            'selected-option' => 'Waar kunnen we u bij helpen?:',

            \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Coachgesprek aanvragen',
            'options' => [
                \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
                \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie gewenst',
                \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...',
            ],
        ],
    ],

    'edit' => [
        'header' => 'Bewerk uw huidige :request_type',
        'text' => 'De gegevens worden uitsluitend door de coöperatie gebruikt om u in uw bewonersreis te ondersteunen. Uw persoonlijke gegevens worden niet doorgegeven aan derden. Meer informatie over de verwerking van uw data en wat we ermee doen kunt u vinden in ons privacy statement.',

        'form' => [
            'allow_access' => 'Ik geef toesteming aan :cooperation om de gegevens uit mijn Hoomdossier in te zien en in overleg met mij deze gegevens aan te passen.',
            'are-you-sure' => 'Weet u zeker dat u de Coöperatie geen toegang wilt geven tot uw dossier?',
            'action' => 'Actie',
            'take-action' => 'Actie ondernemen',
            'message' => 'Uw bericht aan de cooperatie',
            'update' => 'Aanvraag bijwerken <span class="glyphicon glyphicon-envelope"></span>',

            'selected-option' => 'Waar kunnen we u bij helpen?:',

            \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
            \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie gewenst',
            \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...',

            'options' => [
                \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
                \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie gewenst',
                \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...',
            ],
        ],
    ],

    'store' => [
        'success' => 'Uw aanvraag is sucessvol verstuurd, u krijgt zo spoedig mogelijk antwoord. u kunt <strong><a href=":url"">hier uw berichten bekijken</a> </strong> ',
    ],
    'update' => [
        'success' => 'Uw aanvraag is sucessvol bijgewerkt, u krijgt zo spoedig mogelijk antwoord. u kunt <strong><a href=":url"">hier uw berichten bekijken</a> </strong> ',
        'warning' => 'U heeft al een :request_type open staan, u kunt niet meerdere :request_type open hebben staan. Deze moet eerst worden afgehandeld zijn, u kunt deze hier wel bewerken.',
    ],
];
