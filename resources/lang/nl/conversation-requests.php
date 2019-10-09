<?php

return [

    'request-types' => [
        \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
        \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie gewenst',
        \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...',
    ],

    'index' => [
        'header' => 'Actie ondernemen',
        'text' => 'De gegevens worden uitsluitend door de coöperatie gebruikt om u in uw bewonersreis te ondersteunen. Uw persoonlijke gegevens worden niet doorgegeven aan derden. Meer informatie over de verwerking van uw data en wat we ermee doen kunt u vinden in ons privacy statement.',
        'request-coach-conversation' => 'Coachgesprek aanvragen',

        'form' => [
            'no-measure-application-name-title' => 'Contact opnemen',
            'title' => 'Contact over :measure_application_name',
            'allow_access' => 'Ik geef toesteming aan :cooperation om de gegevens uit mijn Hoomdossier in te zien en in overleg met mij deze gegevens aan te passen.',
            'are-you-sure' => 'Weet u zeker dat u de Coöperatie geen toegang wilt geven tot uw dossier?',
            'action' => 'Actie',
            'message' => 'Nadere toelichting op uw vraag',
            'submit' => 'Opsturen <span class="glyphicon glyphicon-envelope"></span>',

            'selected-option' => 'Waar kunnen we u bij helpen?:',
        ],
    ],

    'store' => [
        'success' => 'Uw aanvraag is sucessvol verstuurd, u krijgt zo spoedig mogelijk antwoord. u kunt <strong><a href=":url"">hier uw berichten bekijken</a> </strong> ',
    ],
];
