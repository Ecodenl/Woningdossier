<?php

return [
    'navbar' => [
        'input_source' => 'Invul bron',
        'language' => 'Taal',
        'languages' => [
            'nl' => 'Nederlands',
            'en' => 'Engels',
        ],
    ],
    'building-coach-statuses' => [
        \App\Models\BuildingCoachStatus::STATUS_APPOINTMENT => 'Afspraak is gemaakt',
        \App\Models\BuildingCoachStatus::STATUS_NEW_APPOINTMENT => 'Nieuwe afspraak',
        \App\Models\BuildingCoachStatus::STATUS_DONE => 'Afgehandeld',
        \App\Models\BuildingCoachStatus::STATUS_ACTIVE => 'Actief',
        \App\Models\BuildingCoachStatus::STATUS_REMOVED => 'Verwijderd',

        'awaiting-status' => 'In afwachting',
    ],
    'cooperation' => [
        'create-building' => [
            'current-login-info' => [
                'header' => 'Voor uw huidige login gegevens in.',
            ],
            'building' => [
                'header' => 'Vul uw adres in.',
                'store' => 'Adres aanmaken',
            ],
            'store' => [
                'success' => 'Uw adres is toegevoegd aan het Hoomdossier, u kunt nu gebruik maken van de tool.',
            ],
        ],

        'step' => [
            'general-data' => 'Algemene gegevens',
            'building-detail' => 'Woning details',
        ],

        'chat' => [
            'modal' => [
                'text' => 'Naar welk groepsgesprek wilt u gaan?',
                'public' => 'Publiek',
                'private' => 'Privé',
            ],
            'group-participants' => [
                'revoke-access' => 'Weet u zeker dat u deze gebruiker van de van groeps-chat wilt verwijderen, de gebruiker heeft hierna geen toegang meer tot het gebouw.',
            ],
            'messages' => [
                'participant-added' => ':participant is toegevoegd aan het gesprek',
                'participant-removed' => ':participant is verwijderd uit het gesprek',
            ],
        ],

        'navbar' => [
            'start' => 'Start',
        ],
        'admin' => [
            'example-buildings' => [
                'index' => [
                    'header' => 'Voorbeeldwoningen',
                ],
            ],
            'super-admin' => [
                'side-nav' => [
                    'label' => 'Super admin menu',
                    'home' => 'Home',
                    'cooperations' => 'Cooperaties',
                    'translations' => 'Vertalingen',
                    'example-buildings' => 'Voorbeeld woningen',
                ],
                'index' => [
                    'header' => 'Super admin panel',
                    'text' => 'Beheer de applicatie',
                ],
                'translations' => [
                    'index' => [
                        'header' => 'Stappen waarvan u de vragen kunt vertalen',
                        'text' => 'Hier zijn alle stappen te zien waarvan u vragen en bijbehorende helpteksten kunt aanpassen',
                        'table' => [
                            'columns' => [
                                'name' => 'Stap naam',
                                'actions' => 'Acties',
                            ],
                            'main-translations' => 'Herhalende teksten',
                            'see' => 'Bekijk vertalingen',
                        ],
                    ],
                    'edit' => [
                        'header' => 'Alle vragen die vertaalbaar zijn op de stap :step_name',
                        'sub-question' => 'Laat sub-vragen zien',
                        'question' => 'Vraag in taal: :locale',
                        'help' => 'Helptext in taal: :locale',
                        'search' => [
                            'placeholder' => 'Zoek naar een vraag..',
                        ],
                        'save' => 'Wijzigingen voor de vragen, sub-vragen en helpteksten opslaan.',
                        'close-modal' => 'Sluit venster.',
                    ],
                    'update' => [
                        'success' => 'Vertalingen zijn bijgewerkt.',
                    ],
                ],
                'cooperations' => [
                    'index' => [
                        'header' => 'Cooperaties',
                        'table' => [
                            'columns' => [
                                'name' => 'Cooperatie naam',
                                'slug' => 'Cooperatie Slug / Subdomein',
                                'actions' => 'Acties',
                            ],
                        ],
                        'edit' => 'Bewerken',
                        'create' => 'Aanmaken',
                    ],
                    'create' => [
                        'header' => 'Cooperaie toevoegen',
                        'form' => [
                            'name' => 'Naam van de cooperatie',
                            'slug' => 'Slug / subdomein',
                            'create' => 'Aanmaken',
                        ],
                    ],
                    'edit' => [
                        'header' => 'Cooperatie :name bewerken',
                        'form' => [
                            'name' => 'Naam van de cooperatie',
                            'slug' => 'Slug / subdomein',
                            'update' => 'Bijwerken',
                        ],
                    ],
                    'store' => [
                        'success' => 'Cooperatie is toegevoegd aan het Hoomdossier',
                    ],
                    'update' => [
                        'success' => 'Cooperatie is bijgewerkt.',
                    ],
                ],
            ],
            'navbar' => [
                'current-role' => 'Uw huidige rol:',
                'reports' => 'Rapportages',
                'example-buildings' => 'Example buildings',
            ],

            'custom-fields' => [
                'index' => [
                    'rules' => [
                        'numeric' => 'Getal',
                        'string' => 'Tekst',
                    ],
                    'optional-rules' => [
                        'numeric' => [
                            'between' => 'Tussen',
                            'min' => 'Minimale grootte',
                        ],
                        'string' => [
                            'email' => 'E-mailadres',
                            'max' => 'Maximaal aantal letters',
                        ],
                    ],
                ],
            ],

            'choose-roles' => [
                'header' => 'Als welke rol wilt u doorgaan?',
                'text' => 'Kies hier met welke rol u wilt doorgaan, u kunt dit op elk moment veranderen',
            ],
            'coach' => [
                'side-nav' => [
                    'label' => 'Coach menu',
                    'index' => 'Home',
                    'buildings' => 'Gebouwen',
                    'messages-menu' => 'Berichten menu',
                    'connect-to-resident' => 'Contact maken met bewoners',
                    'messages' => 'Uw berichten',
                    'connect-to-coach' => 'Coach verbinden aan bewoner',
                ],

                'index' => [
                    'header' => 'Welkom op het coach panel',
                    'text' => 'Alle gebouwen waar u toegang tot heeft, u kunt op de pagina voor gebouwen acties uitvoeren hierop.',

                    'table' => [
                        'columns' => [
                            'street' => 'Straatnaam',
                            'city' => 'Stad',
                            'owner' => 'Eigenaar',
                            'actions' => 'Acties',
                        ],
                    ],
                ],

                'buildings' => [
                    'index' => [
                        'no-appointment' => 'Nog geen afspraak',
                        'table' => [
                            'columns' => [
                                'street' => 'Straatnaam',
                                'city' => 'Stad',
                                'owner' => 'Eigenaar',
                                'actions' => 'Acties',
                                'status' => 'Status',
                                'appointment' => 'Datum van afspraak',
                            ],
                            'status' => 'Kies status',
                            'current-status' => 'Huidige status:',
                            'options' => [
                                \App\Models\BuildingCoachStatus::STATUS_APPOINTMENT => 'Afspraak is gemaakt',
                                \App\Models\BuildingCoachStatus::STATUS_NEW_APPOINTMENT => 'Nieuwe afspraak',
                                \App\Models\BuildingCoachStatus::STATUS_DONE => 'Afgehandeld',
                                \App\Models\BuildingCoachStatus::STATUS_ACTIVE => 'Actief',
                                \App\Models\BuildingCoachStatus::STATUS_REMOVED => 'Verwijderd',],
                        ],
                    ],
                    'edit' => [
                        'header' => 'Bewerk de status van het gebouw',
                        'form' => [
                            'status' => 'Status',
                            'appointment-date' => 'Tijdstip van afspraak',
                            'submit' => 'Opslaan',
                            'options' => [
                                \App\Models\BuildingCoachStatus::STATUS_APPOINTMENT => 'Afspraak is gemaakt',
                                \App\Models\BuildingCoachStatus::STATUS_NEW_APPOINTMENT => 'Nieuwe afspraak',
                                \App\Models\BuildingCoachStatus::STATUS_REMOVED => 'Verwijderd',
                                \App\Models\BuildingCoachStatus::STATUS_DONE => 'Afgehandeld',
                            ],
                        ],
                    ],
                    'set-building-status' => [
                        'success' => 'Status is gekoppeld aan het gebouw',
                    ],
                    'fill-for-user' => [
                        'warning' => 'Er is iets fout gegaan, probeer het later opnieuw',
                    ],
                    'header' => 'Gebouwen waar u toegang toe heeft',

                    'details' => [
                        'index' => [
                            'header' => 'Details van het gebouw',
                            'form' => [
                                'submit' => 'Toevoegen',
                            ],
                        ],
                    ],
                ],
                'messages' => [
                    'index' => [
                        'header' => 'Uw berichten / chats',

                        'filter' => [
                            'residents' => 'Filter op gesprekken met bewoners',
                            'coordinators' => 'Filter op gesprekken met coordinatoren',
                        ],
                    ],
                    'edit' => [
                        'header' => 'U gesprek met :firstName :lastName',
                        'send' => 'Verstuur',
                    ],
                ],

                'connect-to-resident' => [
                    'index' => [
                        'header' => 'Bewoners waarmee u een gesprek kunt beginnen',
                        'table' => [
                            'columns' => [
                                'first-name' => 'Voornaam',
                                'last-name' => 'Achternaam',
                                'email' => 'Email',
                                'actions' => 'Acties',
                                'start-conversation' => 'Start gesprek met bewoner',
                            ],
                        ],
                    ],
                    'create' => [
                        'header' => 'Een gesprek beginnen met :firstName :lastName',
                        'form' => [
                            'title' => [
                                'label' => 'Onderwerp van het gesprek',
                                'placeholder' => 'Vul hier het onderwerp van het gesprek in',
                            ],
                            'message' => [
                                'label' => 'Bericht aan de bewoner',
                                'placeholder' => 'Vul hier uw bericht in voor de bewoner',
                            ],
                            'request-type' => [
                                'label' => 'Waar wilde de bewoner het over hebben?',
                                'placeholder' => 'Selecteer optie',
                            ],

                            'options' => [
                                \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie',
                                \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Coachgesprek aanvragen',
                                \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...',
                            ],
                            'submit' => 'Gesprek met bewoner beginnen',
                        ],
                    ],
                    'store' => [
                        'warning' => 'De bewoner is op dit moment niet geïnteresseerd',
                    ],
                ],
            ],

            'cooperation' => [
                'coordination' => [
                    'header' => 'Welkom',
                    'text' => 'U kunt hier verschillende dingen doen.',
                ],
                'users' => [
                    'index' => [
                        'header' => 'Overzicht van alle gebruikers voor uw coöperatie',

                        'table' => [
                            'columns' => [
                                'date' => 'Datum',
                                'name' => 'Naam',
                                'street-house-number' => 'Straat en huisnummer',
                                'zip-code' => 'Postcode',
                                'city' => 'Stadt',
                                'status' => 'Status',
                                'no-known-created-at' => 'Niet bekend'
                            ],
                        ],
                    ],

                    'show' => [
                        'header' => 'Detail overzicht :name, :street-and-number, :zipcode-and-city',

                        'observe-building' => [
                            'label' => 'Woning bekijken',
                            'button' => '<i class="glyphicon glyphicon-eye-open"></i>'
                        ],
                        'delete-account' => [
                            'label' => 'Account verwijderen',
                            'button' => '<i class="glyphicon glyphicon-trash"></i>'
                        ],
                        'role' => [
                            'label' => 'Rol',
                            'button' => 'Bijwerken'
                        ],
                        'status' => [
                            'label' => 'Huidge status: ',
                            'button' => 'Kies status'
                        ],
                        'associated-coach' => [
                            'label' => 'Gekoppelde coaches',
                            'button' => 'Kies coach'
                        ],
                        'appointment-date' => [
                            'label' => 'Datum afspraak',
                            'button' => 'Kies datum'
                        ],

                        'has-building-access' => [
                            'no' => 'Geen toegang tot gebouw',
                            'yes' => 'Toegang tot gebouw'
                        ],

                        'delete-user' => 'Weet u zeker dat u deze gebruiker wilt verwijderen, deze actie kan niet ongedaan worden gemaakt',
                        'revoke-access' => 'Weet u zeker dat u deze gebruiker van de van groeps-chat wilt verwijderen, de gebruiker heeft hierna geen toegang meer tot het gebouw.',
                        'add-with-building-access' => 'Weet u zeker dat u deze gebruiker aan de groeps-chat toegang wilt geven ? De gebruiker heeft hierna ook toegang tot het gebouw',

                        'give-role' => 'Weet u zeker dat u deze gebruiker de rol wilt geven?',
                        'remove-role' => 'Weet u zeker dat u de rol wilt intrekken van deze gebruiker?',

                        'tabs' => [
                            'messages-public' => [
                                'title' => 'Berichten bewoner'
                            ],
                            'messages-intern' => [
                                'title' => 'Berichten intern'
                            ],
                            'comments-on-building' => [
                                'title' => 'Opmerkingen bij woning'
                            ],
                            'fill-in-history' => [
                                'title' => 'Invulhistorie'
                            ]
                        ],
                        'next' => 'Volgende',
                        'previous' => 'Vorige'
                    ],

                    'create' => [
                        'form' => [
                            'first-name' => 'Voornaam',
                            'last-name' => 'Achternaam',
                            'roles' => 'Rol toewijzen aan gebruiker',
                            'email' => 'E-mail adres',
                            'role' => 'Koppel rol aan de nieuwe gebruiker',
                            'select-role' => 'Selecteer een rol...',
                            'password' => [
                                'header' => 'Wachtwoord instellen',
                                'label' => 'Wachtwoord',
                                'placeholder' => 'Wachtwoord invullen...',
                                'help' => 'U kunt het wachtwoord leeg laten, de gebruiker kan deze dan zelf invullen',
                            ],

                            'postal-code' => 'Postcode',
                            'number' => 'Huisnummer',
                            'house-number-extension' => 'Toevoeging',
                            'street' => 'Straat',
                            'city' => 'Plaats',
                            'phone-number' => 'Telefoonnummer',

                            'submit' => 'Gebruiker aanmaken',
                        ],
                    ],

                    'store' => [
                        'success' => 'Gebruiker is toevoegd!',
                    ],
                    'destroy' => [
                        'warning' => 'Let op: dit verwijdert de gebruiker en al zijn gegevens die zijn opgeslagen in het Hoomdossier. Weet u zeker dat u wilt doorgaan?',
                        'success' => 'Gebruiker is verwijderd',
                    ],
                ],
                'cooperation-admin' => [
                    'users' => [
                        'index' => [
                            'header' => 'Overzicht van alle coaches voor uw coöperatie',
                        ],
                    ],
                    'side-nav' => [
                        'label' => 'Coöperatie admin menu',
                        'step' => 'Stappen',
                        'home' => 'Account overzicht',
                        'coaches' => 'Coaches',
                        'create-user' => 'Voeg Coach/bewoner toe',
                        'assign-role' => 'Rollen toewijzen',
                        'messages' => 'Berichten',
                    ],

                    'messages' => [
                        'index' => [
                            'header' => 'Uw berichten / chats',
                        ],
                    ],

                    'index' => [
                        'header' => 'Alle gebruikers van uw coöperatie',

                        'table' => [
                            'columns' => [
                                'date' => 'Datum',
                                'name' => 'Naam',
                                'street-house-number' => 'Straat en huisnummer',
                                'zip-code' => 'Postcode',
                                'city' => 'Stadt',
                                'status' => 'Status'
                            ],
                        ],
                    ],

                    'steps' => [
                        'index' => [
                            'header' => 'Stappen die u kunt beheren',
                            'table' => [
                                'columns' => [
                                    'name' => 'Stap naam',
                                    'active' => 'Actief',
                                ],
                            ],
                        ],
                    ],

                    'assign-roles' => [
                        'index' => [
                            'header' => 'Overzicht gebruikers - rollen toewijzen',

                            'table' => [
                                'columns' => [
                                    'first-name' => 'Voornaam',
                                    'last-name' => 'Achternaam',
                                    'email' => 'E-mail adres',
                                    'role' => 'Huidige rollen van gebruiker',
                                    'actions' => 'Acties',
                                ],
                            ],
                        ],
                        'edit' => [
                            'header' => 'Verander rollen voor :firstName :lastName',

                            'form' => [
                                'first-name' => 'Voornaam',
                                'last-name' => 'Achternaam',
                                'roles' => 'Rol toewijzen aan gebruiker',
                                'email' => 'E-mail adres',
                                'role' => 'Koppel rol aan de nieuwe gebruiker',
                                'select-role' => 'Selecteer een rol...',
                                'password' => [
                                    'header' => 'Wachtwoord instellen',
                                    'label' => 'Wachtwoord',
                                    'placeholder' => 'Wachtwoord invullen...',
                                    'help' => 'U kunt het wachtwoord leeg laten, de gebruiker kan deze dan zelf invullen',
                                ],

                                'submit' => 'Rollen bijwerken',
                            ],
                        ],
                        'update' => [
                            'success' => 'Rollen zijn bijgewerkt',
                        ],
                    ],

                    'reports' => [
                        'title' => 'Rapportages',
                        'description' => 'Rapportage downloads',

                        'download' => [
                            'by-year' => 'Actieplan per jaar',
                            'by-measure' => 'Actieplan per maatregel',
                        ],
                        'csv-columns' => [
                            'first-name' => 'Voornaam',
                            'last-name' => 'Achternaam',
                            'email' => 'Email',
                            'phonenumber' => 'Telefoonnummer',
                            'mobilenumber' => 'Mobiel nummer',
                            'street' => 'Straat',
                            'house-number' => 'Huis nummer',
                            'city' => 'Woonplaats',
                            'zip-code' => 'Postcode',
                            'country-code' => 'Landcode',
                        ],
                    ],
                ],

                'coordinator' => [
                    'users' => [
                        'index' => [
                            'header' => 'Overzicht van alle coaches voor uw coöperatie',
                        ],
                    ],
                    'side-nav' => [
                        'reports' => 'Rapporten',
                        'label' => 'Coördinator menu',
                        'home' => 'Home',
                        'my-messages' => 'Mijn berichten',
                        'questionnaire' => 'Vragenlijsten',
                        'connect-to-coach' => 'Woning toewijzen',
                        'assign-roles' => 'Rollen toewijzen',
                        'messages' => 'Berichten',
                        'buildings' => 'Gebouwen',
                        'coach' => 'Coaches',
                        'add-user' => 'Voeg Coach / Bewoner toe',
                    ],
                    'building-access' => [
                        'index' => [
                            'header' => 'Bewoners die de woning hebben vrijgegeven',
                            'no-appointment' => 'Nog geen afspraak',
                            'table' => [
                                'columns' => [
                                    'street' => 'Straatnaam',
                                    'city' => 'Stad',
                                    'owner' => 'Eigenaar',
                                    'actions' => 'Acties',
                                    'status' => 'Status',
                                    'appointment' => 'Datum van afspraak',
                                ],
                                'multiple-coaches-connected' => 'Er zijn meerdere coaches verbonden met dit gebouw.',
                                'no-coach-connected' => 'Geen coach verbonden met dit gebouw',
                                'no-status-available' => 'Nog geen status beschikbaar',
                                'status' => 'Kies status',
                                'current-status' => 'Huidige status:',
                                'options' => [
                                    \App\Models\BuildingCoachStatus::STATUS_APPOINTMENT => 'Afspraak is gemaakt',
                                    \App\Models\BuildingCoachStatus::STATUS_NEW_APPOINTMENT => 'Nieuwe afspraak',
                                    \App\Models\BuildingCoachStatus::STATUS_DONE => 'Afgehandeld',
                                    \App\Models\BuildingCoachStatus::STATUS_ACTIVE => 'Actief',
                                    \App\Models\BuildingCoachStatus::STATUS_REMOVED => 'Verwijderd',
                                ],
                            ],
                        ],
                        'edit' => [
                            'header' => 'Gebruikers die toegang hebben tot gebouw :street :postal_code',
                            'table' => [
                                'columns' => [
                                    'actions' => 'Acties',
                                    'name' => 'Naam',
                                    'email' => 'Email',
                                ],
                            ],
                        ],
                        'manage-connected-coaches' => [
                            'redirect-message' => 'U kunt een of meerdere gebruikers toegang tot de woning ontzeggen door op het kruisje te klikken bij een gebruiker bovenin het chatvenster.',
                        ],
                        'destroy' => [
                            'success' => 'Toegang is ontzegd',
                        ],
                    ],
                    'reports' => [
                        'title' => 'Rapportages',
                        'description' => 'Rapportage downloads',

                        'download' => [
                            'by-year' => 'Actieplan per jaar',
                            'by-measure' => 'Actieplan per maatregel',
                            'download-questionnaire-results' => 'Download de antwoorden van de bewoners op de custom vragenlijsten',],
                        'csv-columns' => [
                            'first-name' => 'Voornaam',
                            'last-name' => 'Achternaam',
                            'email' => 'Email',
                            'phonenumber' => 'Telefoonnummer',
                            'mobilenumber' => 'Mobiel nummer',
                            'street' => 'Straat',
                            'house-number' => 'Huis nummer',
                            'city' => 'Woonplaats',
                            'zip-code' => 'Postcode',
                            'country-code' => 'Landcode',
                        ],
                    ],
                    'messages' => [
                        'index' => [
                            'header' => 'Uw berichten',
                        ],
                    ],
                    'conversation-requests' => [
                        'index' => [
                            'header' => 'Berichten center',
                            'no-messages' => [
                                'title' => 'Geen openstaande aanvragen',
                                'text' => 'Er zijn op het moment geen openstaande aanvragen',
                            ],
                        ],
                        'show' => [
                            'header' => 'U bent de aanvraag van :firstName :lastName aan het bekijken',
                        ],
                    ],
                    'connect-to-coach' => [
                        'index' => [
                            'header' => 'Overzicht van openstaande gespreks aanvragen',

                            'table' => [
                                'columns' => [
                                    'see-message' => 'Bekijk bericht',
                                    'connect-to-coach' => 'Verbind met coach',
                                    'talk-to-coach' => 'Stuur bericht aan coach',
                                    'type-request' => 'Type aanvraag',
                                    'first-name' => 'Voornaam',
                                    'last-name' => 'Achternaam',
                                    'email' => 'E-mail adres', 'requested-on' => 'Aangevraagd op',
                                    'role' => 'Huidige rollen van gebruiker',
                                    'actions' => 'Acties',
                                ],
                            ],

                            \App\Models\PrivateMessage::REQUEST_TYPE_USER_CREATED_BY_COOPERATION => 'Gebruiker aangemaakt door cooperatie',
                            \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie',
                            \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...',
                            \App\Models\PrivateMessage::REQUEST_TYPE_QUOTATION => 'Offerte',
                            \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Coachgesprek',
                        ],
                        'create' => [
                            'header' => 'U bent de aanvraag van :name aan het koppelen met een coach',

                            'form' => [
                                'message-to-coach' => [
                                    'label' => 'Uw bericht aan de coach',
                                    'placeholder' => 'Bijv: De heer Jan wilde meer weten over vloerisolatie...',
                                ],
                                'title' => 'Titel van het bericht',
                                'select-coach' => 'Selecteer een coach',
                                'submit' => 'Coach koppelen',
                                'submit-warning' => 'Weet u zeker dat u deze coach met :name wilt koppelen?',
                            ],
                        ],
                        'talk-to-coach' => [
                            'header' => 'U kunt hier een bericht naar een coach sturen',

                            'form' => [
                                'message-to-coach' => [
                                    'label' => 'Uw bericht aan de coach',
                                    'placeholder' => 'Bijv: bent u beschikbaar de komende tijd?',
                                ],
                                'title' => 'Titel van het bericht',
                                'select-coach' => 'Naar welke coach wilt u dit bericht sturen?',
                                'submit' => 'Coach bericht sturen',
//							'submit-warning'   => 'Weet u zeker dat u deze coach met :firstName :lastName wilt koppelen?'
                            ],
                        ],
                        'store' => [
                            'success' => 'De coach is verbonden, de coach kan nu verdere actie ondernemen.',
                        ],
                    ],
                    'assign-roles' => [
                        'index' => [
                            'header' => 'Overzicht gebruikers - rollen toewijzen',

                            'table' => [
                                'columns' => [
                                    'first-name' => 'Voornaam',
                                    'last-name' => 'Achternaam',
                                    'email' => 'E-mail adres',
                                    'role' => 'Huidige rollen van gebruiker',
                                    'actions' => 'Acties',
                                ],
                            ],
                        ],
                        'edit' => [
                            'header' => 'Verander rollen voor :firstName :lastName',

                            'form' => [
                                'first-name' => 'Voornaam',
                                'last-name' => 'Achternaam',
                                'roles' => 'Rol toewijzen aan gebruiker',
                                'email' => 'E-mail adres',
                                'role' => 'Koppel rol aan de nieuwe gebruiker',
                                'select-role' => 'Selecteer een rol...',
                                'password' => [
                                    'header' => 'Wachtwoord instellen',
                                    'label' => 'Wachtwoord',
                                    'placeholder' => 'Wachtwoord invullen...',
                                    'help' => 'U kunt het wachtwoord leeg laten, de gebruiker kan deze dan zelf invullen',
                                ],

                                'submit' => 'Rollen wijzigen',
                            ],
                        ],
                        'update' => [
                            'success' => 'Rollen zijn bijgewerkt',
                        ],
                    ],
                    'questionnaires' => [
                        'index' => [
                            'header' => 'Alle vragenlijsten voor uw cooperatie',
                            'table' => [
                                'columns' => [
                                    'questionnaire-name' => 'Vragenlijst naam',
                                    'step' => 'Komt na stap',
                                    'active' => 'Actief',
                                    'actions' => 'Acties',
                                    'see-results' => 'Bekijk resultaten',
                                    'edit' => 'Bewerk vragenlijst',
                                ],
                            ],
                            'types' => [
                                'text' => 'Kort antwoord',
                                'textarea' => 'Alinea',
                                'select' => 'Dropdownmenu',
                                'radio' => 'Selectievakjes',
                                'checkbox' => 'Meerkeuze',
                                'date' => 'Datum',
                            ],
                        ],
                        'edit' => [
                            'types' => [
                                'text' => 'Kort antwoord',
                                'textarea' => 'Alinea',
                                'select' => 'Dropdownmenu',
                                'radio' => 'Meerkeuze',
                                'date' => 'Datum',
                                'checkbox' => 'Selectievakjes',
                            ],
                            'add-validation' => 'Voeg validatie toe',
                            'success' => 'Vragenlijst is bijgewerkt',
                        ],
                    ],
                    'index' => [
                        'header' => 'Alle gebruikers van uw coöperatie',
                        'text' => 'Een overzicht van alle <strong>gebruikers</strong> van uw huidige cooperatie',

                        'table' => [
                            'columns' => [
                                'first-name' => 'Voornaam',
                                'last-name' => 'Achternaam',
                                'email' => 'E-mail adres',
                                'role' => 'Huidige rollen van gebruiker',
                                'actions' => 'Acties',
                            ],
                        ],
                        'create' => [
                            'leave-creation-tool' => 'Keer terug naar overzicht',
                            'leave-creation-tool-warning' => 'Let op!, alle wijzigingen zullen verloren gaan. Uw hiervoor gemaakte formulier is dan niet meer terug te krijgen!',
                        ],
                    ],
                ],
            ],
        ],

        'radiobutton' => [
            'not-important' => 'Niet van toepassing',
            'yes' => 'Ja',
            'no' => 'Nee',
            'unknown' => 'Onbekend',
            'mostly' => 'Gedeeltelijk',
        ],
        'option' => [
            'yes' => 'Ja',
            'no' => 'Nee',
            'unknown' => 'Onbekend',
        ],
        'home' => [
            'tabs' => [
                'start' => 'Start',
                'disclaimer' => 'Disclaimer',
                'bugreport' => 'Bugreport',
                'messages' => 'Berichten',
                'settings' => 'Instellingen',
                'privacy' => 'Privacy',
            ],
        ],
        'help' => [
            'title' => 'Help',
            'help' => [
                'help-with-filling-tool' => 'Ik wil hulp bij het invullen',
                'no-help-with-filling-tool' => 'Ik ga zelf aan de slag',
                'title' => 'Hulp met het gebruik van de tool.',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta, ea exercitationem facilis hic magni mollitia neque, non quo ratione sed sequi similique suscipit ullam unde voluptatibus. Impedit optio quasi tempora?',
            ],
        ],
        'measure' => [
            'title' => 'Maatregelen',
            'measure' => [
                'title' => 'Maatregelen',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta, ea exercitationem facilis hic magni mollitia neque, non quo ratione sed sequi similique suscipit ullam unde voluptatibus. Impedit optio quasi tempora?',
            ],
        ],
        'disclaimer' => [
            'title' => 'Disclaimer',
        ],

        'my-account' => [
            'side-nav' => [
                'home' => 'Home',
                'label' => 'Mijn account',
                'import' => 'Import centrum',
                'settings' => 'Instellingen',
                'access' => 'Gebruikers met toegang tot uw gebouw',
                'my-messages' => 'Mijn berichten',
                'my-requests' => 'Mijn aanvragen',
            ],

            'index' => [
                'header' => 'Mijn account',
                'text' => 'U kunt vanaf hier naar uw instellingen gaan om uw account te wijzigen, voortgang te resetten of om het account te verwijderen. Of u kunt naar uw berichten gaan om deze te zien.',

                'settings' => 'Instellingen <span class="glyphicon glyphicon-cog">',
                'messages' => 'Berichten <span class="glyphicon glyphicon-envelope">',
            ],
            'import-center' => [
                'index' => [
                    'header' => 'Import centrum',
                    'text' => 'Welkom bij het import centrum.',
                    'copy-data' => 'Neem :input_source_name antwoorden over',
                    'other-source' => 'Er zijn gegevens van een :input_source_name aanwezig',
                    'other-source-new' => 'Er zijn <strong>nieuwe</strong> gegevens van een :input_source_name aanwezig',
                    'show-differences' => 'Toon de verschillen met mijn data',
                ],
            ],

            'access' => [
                'index' => [
                    'header' => 'Gebruikers met toegang tot mijn gebouw',
                    'text' => 'Hier ziet uw de gebruikers (Coaches en Coördinatoren), die toegang hebben tot uw gebouw. Deze gebruikers hebben de toegang om uw Hoomdossier in te vullen.',

                    'table' => [
                        'columns' => [
                            'coach' => 'Naam van gebruiker',
                            'actions' => 'Actie ondernemen',
                        ],
                    ],
                ],
            ],

            'messages' => [
                'navigation' => [
                    'inbox' => 'Inbox',
                    'requests' => 'Uw aanvragen',

                    'conversation-requests' => [
                        'request' => 'Coachgesprek aanvragen',
                        'update-request' => 'Coachgesprek aanvraag bijwerken',
//                        'disabled' => 'U heeft al antwoord op uw aanvraag, als deze aanvraag is afgehandeld kunt u een nieuwe indienen'
                        'disabled' => 'Niet beschikbaar',
                    ],
                ],
                'index' => [
                    'header' => 'Mijn berichten',

                    'chat' => [
                        'conversation-requests-consideration' => [
                            'title' => 'Uw aanvraag is in behandeling',
                            'text' => 'Uw aanvraag is in behandeling, er word op het moment voor u een coach uitgekozen die het best bij uw situatie past.',
                        ],
                        'no-messages' => [
                            'title' => 'Geen berichten',
                            'text' => 'Er zijn nog geen berichten. Deze zullen hier verschijnen nadat u antwoord heeft gekregen op een aanvraag voor een coachgesprek of offerte.',
                        ],
                    ],
                ],

                'edit' => [
                    'header' => 'Berichten',

                    'chat' => [
                        'input' => 'Type uw antwoord hier...',
                        'button' => 'Verstuur',
                    ],
                ],

                'requests' => [
                    'index' => [
                        'header' => 'Mijn aanvragen',

                        'chat' => [
                            'conversation-requests-consideration' => [
                                'title' => 'Uw aanvraag is in behandeling',
                                'text' => 'Uw aanvraag is in behandeling, er wordt een coach voor u uitgekozen die het best bij uw situatie past.',
                            ],
                            'no-messages' => [
                                'title' => 'Geen berichten',
                                'text' => 'Er zijn nog geen berichten. Deze zullen hier verschijnen nadat u antwoord heeft gekregen op een aanvraag voor een coachgesprek of offerte.',
                            ],
                        ],
                    ],
                    'update' => [
                        'success' => 'Uw aanvraag is bijgewerkt. u kunt <strong><a href=":url">hier uw berichten bekijken</a> </strong> ',
                    ],
                    'edit' => [
                        'is-connected-to-coach' => 'Deze aanvraag is al gekoppeld aan een coach, u kunt deze dus niet meer bijwerken.',
                    ],
                ],
            ],

            'settings' => [
                'form' => [
                    'index' => [
                        'header' => 'Mijn account',
                        'submit' => 'Update',
                    ],
                    'store' => [
                        'success' => 'Gegevens succesvol gewijzigd',
                    ],
                    'reset-file' => [
                        'header' => 'Uw dossier verwijderen',
                        'description' => '<b>Let op:</b> dit verwijdert alle gegevens die zijn ingevuld bij de verschillende stappen!',
                        'label' => 'Reset mijn dossier',
                        'submit' => 'Reset',
                        'are-you-sure' => 'Let op: dit verwijdert alle gegevens die zijn ingevuld bij de verschillende stappen. Weet u zeker dat u wilt doorgaan?',
                        'success' => 'Uw gegevens zijn succesvol verwijderd van uw account',
                    ],
                    'destroy' => [
                        'header' => 'Account verwijderen',
                        'are-you-sure' => 'Let op: dit verwijdert alle gegevens die wij hebben opgeslagen. Weet u zeker dat u wilt doorgaan?',
                        'label' => 'Mijn account verwijderen',
                        'submit' => 'Verwijderen',
                    ],
                ],
            ],
            'cooperations' => [
                'form' => [
                    'header' => 'Mijn coöperaties',
                ],
            ],
        ],
        'conversation-requests' => [
            'index' => [
                'header' => 'Actie ondernemen',
                'text' => 'De gegevens worden uitsluitend door de coöperatie gebruikt om u in uw bewonersreis te ondersteunen. Uw persoonlijke gegevens worden niet doorgegeven aan derden. Meer informatie over de verwerking van uw data en wat we ermee doen kunt u vinden in ons privacy statement.',

                'form' => [
                    'no-measure-application-name-title' => 'Gesprek aanvragen',
                    'title' => 'Actie ondernemen met :measure_application_name',
                    'allow_access' => 'Ik geef toesteming aan :cooperation om de gegevens uit mijn Hoomdossier in te zien en in overleg met mij deze gegevens aan te passen.',
                    'are-you-sure' => 'Weet u zeker dat u de Coöperatie geen toegang wilt geven tot uw dossier?',
                    'action' => 'Actie',
                    'take-action' => 'Actie ondernemen',
                    'message' => 'Nadere toelichting op uw vraag',
                    'submit' => 'Opsturen <span class="glyphicon glyphicon-envelope"></span>',

                    'selected-option' => 'Waar kunnen we u bij helpen?:',
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
        ],
        'tool' => [
            'current-building-address' => 'Huidig adres: :street :number, :zip_code :city',
            'filling-for' => 'U bewerkt de woning van :first_name :last_name als :input_source_name.',
            'change-interest' => 'U heeft in de eerste stap uw interesse over :item aangegeven, u kunt deze hier veranderen of zo laten.',
            'is-user-comparing-input-sources' => 'U bent nu de data aan het vergelijken, de velden die rood zijn gemarkeerd bevat een andere waarde',

            'back-to-overview' => 'Terug naar overzicht',

            'unit' => [
                'year' => 'jaar',
                'liter' => 'liter',
                'day' => 'dag',
                'pieces' => 'stuks',
                'square-meters' => 'm<sup>2</sup>',
                'cubic-meters' => 'm<sup>3</sup>',
                'co2' => 'CO<sub>2</sub>',
                'kilograms' => 'kg',
                'degrees' => 'graden',
                'kwh' => 'kWh',
                'hours' => 'uren',
            ],

            'title' => 'Basisadvies',

            'general-data' => [
                'title' => 'Algemene gegevens',
                'coach-input' => [
                    'copy' => [
                        'title' => 'Neem coach antwoorden over',
                        'help' => 'Weet u zeker dat u alle antwoorden van de coach wilt overnemen? Uw huidige antwoorden zullen worden overschreven door die van de coach.',
                    ],
                ],

                'example-building' => [
                    'example-building-type' => 'Kies de best passende voorbeeldwoning',
                ],
            ],

            'wall-insulation' => [
                'intro' => [
                    'title' => 'Gevelisolatie',
                ],
            ],

            'insulated-glazing' => [
                'title' => 'Isolerende beglazing',
            ],

            'floor-insulation' => [
                'title' => 'Vloerisolatie',

                'intro' => [
                    'title' => 'Vloerisolatie',
                ],

            ],

            'roof-insulation' => [
                'no-roof' => 'Dit veld is verplicht als u een dak type heeft gekozen',
                'title' => 'Dakisolatie',
            ],

            'boiler' => [
                'title' => 'HR CV Ketel',

                'already-efficient' => 'Het vervangen van de huidige ketel zal alleen een beperkte energiebesparing opleveren omdat u al een HR ketel hebt.',
            ],

            'solar-panels' => [
                'title' => 'Zonnepanelen',
                'advice-text' => 'Voor het opwekken van uw huidige elektraverbruik heeft u in totaal ca. :number zonnepanelen in optimale oriëntatie nodig.',
                'total-power' => 'Totale Wp vermogen van de installatie: :wp',

                'indication-for-costs' => [
                    'performance' => [
                        'ideal' => 'Ideaal',
                        'possible' => 'Mogelijk',
                        'no-go' => 'Onrendabel',
                    ],
                ],
            ],

            'heater' => [
                'title' => 'Zonneboiler',
            ],

            'my-plan' => [
                'options' => [
                    \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
                    \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie gewenst',
                    \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...',
                ],
                'warnings' => [
                    'title' => 'Let op!',
                ],

                //'description' => 'Op deze pagina ziet u een samenvatting van alle maatregelen die u in het hoomdossier volledig hebt ingevuld. Per maatregel ziet u wat de indicatieve kosten en besparingen zijn.<br><br>Op basis van deze uitkomsten kunt u uw persoonlijke stappenplan voor de komende jaren samenstellen. Hiervoor selecteert u een maatregel in de eerste kolom (“Interesse”) en voert in de laatste kolom (“Planning”) het jaartal in wanneer u deze maatregel uit zou willen voeren.<br><br>Onder aan de pagina wordt dan uw stappenplan weergegeven. Per jaar kunt u zien hoe veel geld u voor onderhoud en energiebesparende maatregelen zou moeten reserveren en wat u aan besparing op uw energierekening in dit jaar zou kunnen verwachten.',

                'no-year' => 'Geen jaartal',
                'add-comment' => 'Opmerking opslaan',

                'coach-comments' => [
                    'general-data' => 'Algemene gegevens',
                    'wall-insulation' => 'Gevelisolatie',
                    'floor-insulation' => 'Vloerisolatie',
                    'insulated-glazing' => 'Isolerende beglazing',
                    'roof-insulation-hellend-dak' => 'Dakisolatie - Hellend dak',
                    'roof-insulation-plat-dak' => 'Dakisolatie - Plat dak',
                    'high-efficiency-boiler' => 'HR Ketel',
                ],

                'conversation-requests' => [
                    'request' => 'Coachgesprek aanvragen',
                    'update-request' => 'Coachgesprek aanvraag bijwerken',
                    'disabled' => 'Niet beschikbaar',
                ],
                'conversation-requests-request' => 'Coachgesprek aanvraag',
                'conversation-requests-request-update' => 'Coachgesprek aanvraag bijwerken',

                'csv-columns' => [
                    'year-or-planned' => 'Jaar / gepland jaar',
                    'interest' => 'Interesse',
                    'measure' => 'Maatregel',
                    'costs' => 'Kosten',
                    'savings-gas' => 'Besparing m3 gas',
                    'savings-electricity' => 'Besparing kWh elektra',
                    'savings-costs' => 'Besparing in euro',
                    'advice-year' => 'Geadviseerd jaar',
                    'planned-year' => 'Planning',
                    'costs-advice-year' => 'Kosten in geadviseerd jaar',
                ],
            ],

            'ventilation-information' => [

                'downloads' => [
                    'title' => 'Downloadbare informatie.',
                    'content' => 'Pdf informatie...',
                ],
            ],

            'heat-pump-information' => [
                'title' => 'Informatie pagina over warmtepomp.',
                'description' => '',
                'downloads' => [
                    'title' => 'Downloadbare informatie.',
                    'content' => 'Pdf informatie...',
                ],
            ],

            'heat-pump' => [
                'current-gas-usage' => 'Huidig gasverbruik',
                'heat-pump-type' => 'Kies de soort warmtepomp',
                'gas-usage-for-tapwater' => 'Gasgebruik voor warm tapwater',
                'gas-usage-for-heating' => 'Gasgebruik voor de verwarming',

                'net-gas-usage' => 'Netto gasgebruik obv rendement',
                'energy-content' => 'Energieinhoud',
                'heat' => 'Warmte',
                'cop' => 'COP',
                'electro-usage-heatpump' => 'Elektragebruik door de warmtepomp',

                'hybrid-heatpump' => [
                    'title' => 'Hybride warmtepomp met buitenlucht als warmtebron',
                    'indication-for-costs' => [
                        'title' => 'Indicatie voor kosten en baten voor deze maatregel',
                        'gas-savings' => 'Gasbesparing',
                        'co2-savings' => 'CO<sub>2</sub> Besparing',
                        'savings-in-euro' => 'Besparing in €',
                        'moreusage-electro-in-euro' => 'Meerverbruik in elektra in €',
                        'electro-usage-heatpump' => 'Elektragebruik door de warmtepomp',
                        'saldo' => 'Saldo',
                        'indicative-costs' => 'Indicatieve kosten',
                        'comparable-rate' => 'Vergelijkbare rente',
                        'year' => 'Jaar',
                    ],
                ],
                'full-heatpump' => [
                    'title' => 'Volledige heatpump',
                    'current-heating' => 'Hoe word de woning nu verwarmd?',
                    'wanted-heat-source' => 'Welke soort warmtebron is gewenst?',
                    'heat-usage' => [
                        'heater' => 'Warmtegebruik voor verwarming',
                        'warm-tapwater' => 'Warmtegebruik voor warm tapwater',
                    ],
                    'indication-for-costs' => [
                        'title' => 'Indicatie voor kosten en baten voor deze maatregel',
                        'gas-savings' => 'Gasbesparing',
                        'co2-savings' => 'CO<sub>2</sub> Besparing',
                        'savings-in-euro' => 'Besparing in €',
                        'moreusage-electro-in-euro' => 'Meerverbruik in elektra in €',
                        'electro-usage-heatpump' => 'Elektragebruik door de warmtepomp',
                        'saldo' => 'Saldo',
                        'indicative-costs' => 'Indicatieve kosten',
                        'comparable-rate' => 'Vergelijkbare rente',
                        'year' => 'Jaar',
                    ],
                ],
            ],
        ],
    ],
];
