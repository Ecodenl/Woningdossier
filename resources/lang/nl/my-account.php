<?php

return [
    'side-nav' => [
        'home'          => 'Home',
        'notification-settings' => 'Notificatie instellingen',
        'label'       => 'Mijn account',
        'import'      => 'Import centrum',
        'settings'    => 'Instellingen',
        'hoom-settings' => 'Hoomdossier instellingen',
        'access'      => 'Gebruikers met toegang tot uw woning',
        'my-messages' => 'Mijn berichten',
        'my-requests' => 'Mijn aanvragen',
    ],

    'index'         => [
        'header' => 'Mijn account',
        'text'   => 'U kunt vanaf hier naar uw instellingen gaan om uw account te wijzigen, voortgang te resetten of om het account te verwijderen. Of u kunt naar uw berichten gaan om deze te zien.',

        'settings' => 'Instellingen <span class="glyphicon glyphicon-cog">',
        'messages' => 'Berichten <span class="glyphicon glyphicon-envelope">',
    ],
    'import-center' => [
        'index' => [
            'header'           => 'Import centrum',
            'text'             => 'Welkom bij het import centrum.',
            'copy-data'        => 'Neem :input_source_name antwoorden over',
            'other-source'     => 'Er zijn gegevens van een :input_source_name aanwezig',
            'other-source-new' => 'Er zijn <strong>nieuwe</strong> gegevens van een :input_source_name aanwezig',
            'show-differences' => 'Toon de verschillen met mijn data',
        ],
    ],
    'notification-settings' => [
        'index' => [
            'header' => 'Uw notificatie instellingen',
            'text' => 'U kunt deze hier inzien, en zo nodig wijzigen',
            'table' => [
                'columns' => [
                    'name' => 'Notificatie soort',
                    'interval' => 'Interval',
                    'last-notified-at' => 'Laatst gestuurd op',
                    'actions' => 'Actie',
                ],
                'never-sent' => 'Notificatie is nooit verstuurd',
            ],
        ],
        'show' => [
            'header' => 'Bewerk notificatie',
            'form' => [
                'interval' => 'Notificatie interval voor :type',
                'submit' => 'Interval opslaan',
            ],
        ],
        'update' => [
            'success' => 'Interval is opgeslagen.',
        ],
    ],

    'access' => [
        'index' => [
            'form' => [
                'allow_access' => 'Ik geef toesteming aan :cooperation om de gegevens uit mijn Hoomdossier in te zien en in overleg met mij deze gegevens aan te passen.',
            ],
            'header' => 'Gebruikers met toegang tot mijn woning',
            'text-allow-access'   => 'De gegevens worden uitsluitend door de coöperatie gebruikt om u in uw bewonersreis te ondersteunen. Uw persoonlijke gegevens worden niet doorgegeven aan derden. Meer informatie over de verwerking van uw data en wat we ermee doen kunt u vinden in ons privacy statement.',
            'text'   => 'Hier ziet uw de gebruikers (Coaches en Coördinatoren), die toegang hebben tot uw woning. Deze gebruikers hebben de toegang om uw Hoomdossier in te vullen.',
            'table' => [
                'columns' => [
                    'coach'   => 'Naam van gebruiker',
                    'actions' => 'Actie ondernemen',
                ],
            ],
        ],
    ],

    'messages' => [
        'navigation' => [
            'inbox'    => 'Inbox',
            'requests' => 'Uw aanvragen',

            'conversation-requests' => [
                'request'        => 'Coachgesprek aanvragen',
                'update-request' => 'Coachgesprek aanvraag bijwerken',
                'disabled'       => 'Niet beschikbaar',
            ],
        ],
        'index'      => [
            'header' => 'Mijn berichten',

            'chat' => [
                'conversation-requests-consideration' => [
                    'title' => 'Uw aanvraag is in behandeling',
                    'text'  => 'Uw aanvraag is in behandeling, er word op het moment voor u een coach uitgekozen die het best bij uw situatie past.',
                ],
                'no-messages'                         => [
                    'title' => 'Geen berichten',
                    'text'  => 'Er zijn nog geen berichten. Deze zullen hier verschijnen nadat u antwoord heeft gekregen op een aanvraag voor een coachgesprek of offerte.',
                ],
            ],
        ],

        'edit' => [
            'header' => 'Berichten',

            'chat' => [
                'input'  => 'Type uw antwoord hier...',
                'button' => 'Verstuur',
            ],
        ],

        'requests' => [
            'index'  => [
                'header' => 'Mijn aanvragen',

                'chat' => [
                    'conversation-requests-consideration' => [
                        'title' => 'Uw aanvraag is in behandeling',
                        'text'  => 'Uw aanvraag is in behandeling, er wordt een coach voor u uitgekozen die het best bij uw situatie past.',
                    ],
                    'no-messages'                         => [
                        'title' => 'Geen berichten',
                        'text'  => 'Er zijn nog geen berichten. Deze zullen hier verschijnen nadat u antwoord heeft gekregen op een aanvraag voor een coachgesprek of offerte.',
                    ],
                ],
            ],
            'update' => [
                'success' => 'Uw aanvraag is bijgewerkt. u kunt <strong><a href=":url">hier uw berichten bekijken</a> </strong> ',
            ],
            'edit'   => [
                'is-connected-to-coach' => 'Deze aanvraag is al gekoppeld aan een coach, u kunt deze dus niet meer bijwerken.',
            ],
        ],
    ],

    'settings'     => [
        'index' => [
            'header' => 'Gebruikergegevens',
            'text' => 'Hier kunt u uw gebruikers gegevens aanpassen, deze zijn per coöperatie aanpasbaar. Wat u dus hier aanpast heeft geen invloed op de andere coöperaties waar u bij bent aangesloten.',
            'header-building' => 'Adres',
            'form' => [
                'submit' => 'Update gevens',
                'building' => [
                    'street' => 'Straat',
                    'number' => 'Huisnummer',
                    'extension' => 'Toevoeging',
                    'postal-code' => 'Postcode',
                    'city' => 'Stad',
                ],
                'user' => [
                    'first-name'            => 'Voornaam',
                    'last-name'             => 'Achternaam',
                    'phone_number'          => 'Telefoonnummer',
                ],
            ],
        ],
        'store'      => [
            'success' => 'Gegevens succesvol gewijzigd',
        ],
        'reset-file' => [
            'header'       => 'Dossier resetten',
            'description'  => '<b>Let op:</b> dit verwijdert alle gegevens die zijn ingevuld bij de verschillende stappen!',
            'label'        => 'Reset mijn dossier',
            'submit'       => 'Reset',
            'are-you-sure' => 'Let op: dit verwijdert alle gegevens die zijn ingevuld bij de verschillende stappen. Weet u zeker dat u wilt doorgaan?',
            'success'      => 'Uw gegevens zijn succesvol verwijderd van uw account',
        ],
        'destroy'    => [
            'header'       => 'Account verwijderen',
            'are-you-sure' => [
                'complete-delete' => 'Let op: dit verwijdert alle gegevens die wij hebben opgeslagen. Weet u zeker dat u wilt doorgaan?',
                'delete-from-cooperation' => 'Let op: u bent bij meerdere cooperaties aangesloten, dit verwijdert uw account alleen bij de huidige coöperatie. Om uw account volledig te verwijderen dient u deze stap bij elke coöperatie uit te voeren.',
            ],

            'label'        => 'Mijn account verwijderen',
            'submit'       => 'Verwijderen',
            'success'      => [
                'cooperation' => 'Uw account is succesvol verwijderd voor deze coöperatie',
                'full' => 'Uw account is volledig verwijderd',
            ],
        ],
    ],
    'hoom-settings'     => [
        'index' => [
            'header' => 'Accountgegevens',
            'header-password' => 'Wachtwoord aanpassen',
            'text' => 'Hier kunt u uw account gegevens aanpassen, deze hebben effect op het gehele Hoomdossier. Als u hier uw e-mail of / en wachtwoord aanpast, dan geld dit voor elke coöperatie waar u bij bent aangesloten.',
            'form' => [
                'submit' => 'Update gevens',

                'account' => [
                    'e-mail'                => 'E-mailadres',
                    'password'              => 'Wachtwoord',
                    'password-confirmation' => 'Wachtwoord (bevestigen)',
                    'new-password'          => 'Nieuw wachtwoord',
                    'new-password-confirmation' => 'Nieuw wachtwoord (bevestigen)',
                    'current-password'      => 'Huidig wachtwoord',
                ],
            ],
        ],
        'store'      => [
            'success' => 'Gegevens succesvol gewijzigd',
        ],
    ],
];
