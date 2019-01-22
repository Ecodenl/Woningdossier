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
    'cooperation' => [
        'navbar' => [
            'start' => 'Start',
        ],
        'admin' => [
            'navbar' => [
                'current-role' => 'Uw huidge rol:',
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
                'header' => 'Als welke rol wilt u doorgaan ?',
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
                                \App\Models\BuildingCoachStatus::STATUS_REMOVED => 'Verwijderd', ],
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
                                'label' => 'Waar wilde de bewoner het over hebben ?',
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
                'cooperation-admin' => [
                    'side-nav' => [
                        'label' => 'Coöperatie admin menu',
                        'step' => 'Stappen',
                        'home' => 'Home',
                        'users' => 'Gebruikers van uw coöperatie',
                        'create-user' => 'Voeg een gebruiker toe',
                        'assign-role' => 'Rollen toewijzen',
                    ],

                    'index' => [
                        'header' => 'Welkom op het coöperatie admin panel',

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

                    'users' => [
                        'index' => [
                            'header' => 'Overzicht van alle gebruikers binnen uw coöperatie',

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
                        'create' => [
                            'header' => 'Gebruiker toevoegen aan uw coöperatie',

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

                                'submit' => 'Gebruiker aanmaken',
                            ],
                        ],

                        'store' => [
                            'success' => 'Gebruiker is met success toevoegd',
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
                    'side-nav' => [
                        'reports' => 'Rapporten',
                        'label' => 'Coördinator menu',
                        'home' => 'Home',
                        'my-messages' => 'Mijn berichten',
                        'questionnaire' => 'Vragenlijsten',
                        'connect-to-coach' => 'Gewbouw toewijzen',
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
                            'download-questionnaire-results' => 'Download de antwoorden van de bewoners op de custom vragenlijsten', ],
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
                            'header' => 'Overzicht van openstaande gespreks aanvragen',
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

                            \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie',
                            \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...',
                            \App\Models\PrivateMessage::REQUEST_TYPE_QUOTATION => 'Offerte',
                            \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Coachgesprek',
                        ],
                        'create' => [
                            'header' => 'U bent de aanvraag van :firstName :lastName aan het koppelen met een coach',

                            'form' => [
                                'message-to-coach' => [
                                    'label' => 'Uw bericht aan de coach',
                                    'placeholder' => 'Bijv: De heer Jan wilde meer weten over vloerisolatie...',
                                ],
                                'title' => 'Titel van het bericht',
                                'select-coach' => 'Selecteer een coach',
                                'submit' => 'Coach koppelen',
                                'submit-warning' => 'Weet u zeker dat u deze coach met :firstName :lastName wilt koppelen ?',
                            ],
                        ],
                        'talk-to-coach' => [
                            'header' => 'U kunt hier een bericht naar een coach sturen',

                            'form' => [
                                'message-to-coach' => [
                                    'label' => 'Uw bericht aan de coach',
                                    'placeholder' => 'Bijv: bent u beschikbaar de komende tijd ?',
                                ],
                                'title' => 'Titel van het bericht',
                                'select-coach' => 'Naar welke coach wilt u dit bericht sturen ?',
                                'submit' => 'Coach bericht sturen',
//							'submit-warning'   => 'Weet u zeker dat u deze coach met :firstName :lastName wilt koppelen ?'
                            ],
                        ],
                        'store' => [
                            'success' => 'Uw bericht is verstuurd naar de coach, de coach kan nu verdere actie ondernemen.',
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
                    'user' => [
                        'index' => [
                            'header' => 'Overzicht van alle gebruikers voor uw coöperatie',

                            'table' => [
                                'columns' => [
                                    'first-name' => 'Voornaam',
                                    'last-name' => 'Achternaam',
                                    'email' => 'E-mail adres',
                                    'role' => 'Huidige rollen van gebruiker',
                                    'total-houses' => 'Totale woningen',
                                    'actions' => 'Acties',
                                ],
                            ],
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
                            'success' => 'Gebruiker is met success toevoegd',
                        ],
                        'destroy' => [
                            'success' => 'Gebruiker is verwijderd',
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
                            'leave-creation-tool-warning' => 'Letop!, alle wijzigingen zullen verloren gaan. U hiervoor gemaakte formulier is niet meer terug te krijgen',
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
            'start' => [
                'best-user' => '<b>Beste gebruiker</b>, <br><br> Hartelijk welkom in uw Hoomdossier! Hier kunt u de huidige situatie van uw woning in kaart brengen, kijken welke energiebesparende maatregelen interessant voor u kunnen zijn en het onderhoud van uw woning plannen:',
                'get-started' => 'Meteen aan de slag!',
                'by' => 'Het Hoomdossier wordt u aangeboden door :cooperation om u bij de verduurzaming van uw woning te ondersteunen. Mocht u vragen hebben over energiebesparende maatregelen, lopende wijkacties of hulp nodig hebben bij het invullen van het Hoomdossier, dan kunt u hier contact opnemen met uw coöperatie:',
                'contact' => 'Contact opnemen met :cooperation',
                'feedback' => 'Het Hoomdossier wordt continu doorontwikkeld en verbeterd. Praktijkervaringen van gebruikers zijn hierbij een belangrijke informatiebron om het pakket steeds verder te verbeteren. Als u opmerkingen en of vragen over het Hoomdossier heeft of iets niet naar verwachting werkt kunt u uw feedback hier geven:',
                'give-feedback' => 'Feedback geven op het Hoomdossier',
            ],
            'privacy' => [
                'description' => '<b>Privacy statement</b>
                                <br>
                                <br>
                                De gegevens in het Hoomdossier worden uitsluitend gedeeld met de coöperatie :cooperation.

                                We gaan zorgvuldig met uw gegevens om en beloven u dat:
                                <ul>
                                    <li>we uw gegevens uitsluitend gebruiken om u te kunnen adviseren en ondersteunen bij  het opstellen en uitvoeren van projecten voor het verduurzamen van uw woning;</li>
                                    <li>we uw persoonsgegevens nooit zullen delen met derden (bijvoorbeeld bedrijven) zonder dat u uw aanvullende toestemming hebt gegeven;</li>
                                    <li>wanneer we gegevens  gebruiken t.b.v. analyses of rapportages, deze nooit tot uw individuele gegevens terug te herleiden zijn.</li>
                                </ul>
                                <br>
                                <br>
                                U kunt toestemming geven aan de coöperatie :cooperation om uw dossier op afstand in te zien en u bij het invullen te helpen. Een dergelijke toestemming kan op elk moment ingetrokken worden en geldt uitsluitend voor de gegevens van het Basisadvies, uw accountgegevens en het berichtenverkeer met de coöperatie.
                                <br>
                                <br>
                                U hebt de mogelijkheid om uw account te verwijderen of om alle toegevoegde informatie te wissen onder behoud van het account. Een dergelijke actie betekent dat al uw gegevens uit het Hoomdossier worden verwijderd.
                                <br>
                                <br>
                                Gegevens die in het Hoomdossier opgeslagen worden:
                                <br>
                                <br>
                                In het Hoomdossier worden gegevens over uw woning, uw energieverbruik en uw gebruiksgedrag opgeslagen. De gegevens worden gebruikt om u te adviseren welke energiebesparende maatregelen u kunt nemen in uw woning en wat de indicatieve kosten en baten van deze maatregelen zijn.
                                <br>
                                <br>
                                De volgende gegevens worden in het Hoomdossier opgeslagen:
                                <br>
                                <br>
                                <ul>
                                    <li>NAW gegevens</li>
                                    <li>Algemene gegevens van de woning</li>
                                    <li>Welke energiebesparende maatregelen al zijn genomen</li>
                                    <li>Interesse voor een maatregel (Ja/nee)</li>
                                    <li>Gegevens over het gebruik van de woning</li>
                                    <li>Indicatieve kosten van te nemen maatregelen</li>
                                    <li>Indicatie van besparing gas per maatregel</li>
                                    <li>Indicatie van besparing elektra per maatregel</li>
                                    <li>Indicatieve financiële besparing per maatregel</li>
                                    <li>Indicatieve CO2 besparing per maatregel</li>
                                    <li>Geadviseerd uitvoeringsjaar</li>
                                    <li>Zelf ingevuld uitvoeringsjaar</li>
                                </ul>',
            ],
            'disclaimer' => [
                'description' => '<b>Disclaimer</b><br><br>Het woondossier maakt gebruik van formules en vergelijkingen die een benadering zijn van de werkelijkheid. Hoewel het woondossier dus wel inzicht geeft in de potentiele impact van energiebesparende maatregelen, kan het een persoonlijk advies op maat niet vervangen. In overleg met uw coöperatie kunt u het woondossier gebruiken als basis voor een keukentafelgesprek of een professioneel advies.<br><br>Er kan geen garantie aan de resultaten van het woondossier ontleend worden ten aanzien van de daadwerkelijke energieprestaties, berekende energiegebruik of besparingen. De essentie van het rekenen met het woondossier is het krijgen van inzicht in consequenties van het nemen van maatregelen.',
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
                ]
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

            'edit-conversation-requests' => 'U kunt uw huidige aanvragen <strong><a href="'.route('cooperation.my-account.messages.requests.index').'">hier bekijken</a></strong> ',
        ],
        'tool' => [
            'current-building-address' => 'Huidig adres: :street :number, :zip_code :city',
            'filling-for' => 'U bewerkt de woning van :first_name :last_name als :input_source_name.',
            'change-interest' => 'U heeft in de eerste stap uw interesse over :item aangegeven, u kunt deze hier veranderen of zo laten.',
            'is-user-comparing-input-sources' => 'U bent nu de data aan het vergelijken, de velden die rood zijn gemarkeerd bevat een andere waarde',

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
                        'help' => 'Weet u zeker dat u alle antwoorden van de coach wil overnemen ? Al uw huidige antwoorden zullen worden overschreven door die van de coach.',
                    ],
                ],

                'example-building' => [
                    'example-building-type' => 'Kies de best passende voorbeeldwoning',
                    'no-match' => 'Er is geen passende voorbeeldwoning',
                    'apply-are-you-sure' => 'Weet u zeker dat u deze voorbeeldwoning wilt toepassen?',
                ],
            ],

            'wall-insulation' => [
                'intro' => [
                    'title' => 'Gevelisolatie',
                ],

                'alert' => [
                    'description' => 'Let op: geverfde of gestukte gevels kunnen helaas niet voorzien worden van spouwmuurisolatie',
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

                'has-crawlspace' => [
                    'no-crawlspace' => 'De vloer kan alleen van boven af geïsoleerd worden. Let op de hoogtes bij deuren en bij de trap. Vraag om aanvullend advies.',
                ],
                'crawlspace-access' => [
                    'no-access' => 'Er is aanvullend onderzoek nodig. Om de vloer te kunnen isoleren moet eerst een kruipluik gemaakt worden.',
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
                'amount' => 'stuks',
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
                    'check-order' => 'U probeert dakisolatie met vervanging van de dakbedekking te plannen, maar de onderhoudsmaatregel voor het vervangen van de dakpannen of dakbedekking staat uit!',
                    'planned-year' => 'De uitvoeringsjaren van de energiebesparende maatregel en de onderhoudsmaatregel zijn niet gelijk!',
                ],

                'title' => 'Actieplan',
                'description' => 'Op deze pagina ziet u een samenvatting van alle maatregelen die u in het hoomdossier volledig hebt ingevuld. Per maatregel ziet u wat de indicatieve kosten en besparingen zijn.<br><br>Op basis van deze uitkomsten kunt u uw persoonlijke stappenplan voor de komende jaren samenstellen. Hiervoor selecteert u een maatregel in de eerste kolom (“Interesse”) en voert in de laatste kolom (“Planning”) het jaartal in wanneer u deze maatregel uit zou willen voeren.<br><br>Onder aan de pagina wordt dan uw stappenplan weergegeven. Per jaar kunt u zien hoe veel geld u voor onderhoud en energiebesparende maatregelen zou moeten reserveren en wat u aan besparing op uw energierekening in dit jaar zou kunnen verwachten.',
                'energy-saving-measures' => 'Energiebesparende maatregelen',
                'maintenance-measures' => 'Onderhoud',

                'maintenance-plan' => 'Uw persoonlijke meerjarenonderhoudsplan',
                'no-year' => 'Geen jaartal',
                'download' => 'Download hier je actieplan',

                'coach-comments' => [
                    'title' => 'Opmerkingen die door de coach zijn geplaatst',
                    'general-data' => 'Algemene gegevens',
                    'wall-insulation' => 'Gevelisolatie',
                    'floor-insulation' => 'Vloerisolatie',
                    'insulated-glazing' => 'Isolerende beglazing',
                    'roof-insulation-hellend-dak' => 'Dakisolatie - Hellend dak',
                    'roof-insulation-plat-dak' => 'Dakisolatie - Plat dak',
                    'high-efficiency-boiler' => 'HR Ketel',
                ],

                'conversation-requests' => [
                    'take-action' => 'Actie ondernemen',
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
                'columns' => [
                    'more-info' => 'Meer info',
                    'interest' => 'Interesse',
                    'measure' => 'Maatregel',
                    'costs' => 'Kosten',
                    'savings-gas' => 'Besparing m<sup>3</sup> gas',
                    'savings-electricity' => 'Besparing kWh elektra',
                    'savings-costs' => 'Besparing in euro',
                    'advice-year' => 'Geadviseerd',
                    'planned-year' => 'Planning',
                    'take-action' => 'Actie ondernemen',
                ],
            ],

            'ventilation-information' => [
                'title' => 'Informatie pagina over ventilatie.',
                'description' => 'Voor uw gezondheid is schone lucht noodzakelijk. Goede ventilatie in uw woning hoort daarbij, maar vaak wordt er te weinig geventileerd. Schimmel, tabaksrook en fijnstof zijn veel voorkomende vervuiling in woningen. Vervuilde lucht in huis versterkt allergieën, luchtwegproblemen en irritatie van slijmvliezen, zeker bij de oudere generatie. Zorg dus voor voldoende ventilatie in uw woning.<br><br>In oude huizen gaat de luchtverversing in veel situaties vanzelf via naden en kieren. Dat is echter niet zo goed voor het comfort en zorgt voor een hoog energiegebruik. Daarom worden de huizen steeds luchtdichter gemaakt en van goede isolatie voorzien. Om een gezond binnenklimaat te bereiken is hierbij een andere vorm van ventilatie nodig. Vraag gestuurd moet vuile lucht het huis uit en schone lucht moet aangevoerd worden. Ventilatielucht opwarmen kost energie, maar het is geen verspilling: het is hard nodig voor uw gezondheid.<br><br>Hieronder leggen we twee veel voorkomende basisprincipes van ventilatie uit en geven een aantal tips voor een goed binnenklimaat.<br><br><h4>Natuurlijke ventilatie</h4>In een huis met natuurlijke ventilatie zit geen mechanisch ventilatiesysteem, dat betekent dat er alleen via natuurlijke weg geventileerd kan worden door ventilatieroosters en bijvoorbeeld ramen of deuren open te zetten. Meestal is er bij oude huizen sprake van een bepaalde basisventilatie door naden, kieren. Het verbeteren van de kierdichtheid vermindert de natuurlijke ventilatie in huis. Bewust of vraag gestuurd ventileren wordt dan de beste weg om zo zuinig mogelijk een gezonde leefomgeving te houden.<br><br><strong>Hoe kunt u luchten</strong>Ventileren kan door ramen en deuren tegen elkaar open te zetten, en de verwarming uit te zetten. We noemen dat luchten of spuien. De in het vertrek aanwezige waterdamp, die anders in de muren zou trekken en tegen de ramen kan condenseren, wordt met de vervuilde lucht afgevoerd. De verse lucht wordt opgewarmd door de warmte die nog in muren en plafond aanwezig is. Dit luchten hoeft niet heel lang te duren. In de winter korter dan in de zomer, afhankelijk van de buitentemperatuur. Lucht de woonvertrekken vooral in de koude perioden kort maar goed, door zoveel mogelijk ramen open te zetten. Een goed tijdstip om woonvertrekken extra te luchten is voor het naar bed gaan, als de kachel lager staat.<br><br><strong>Slaapkamers</strong>Slaapkamers kunnen het beste in de ochtend gelucht worden voor ongeveer 20 minuten (wat korter in de winter, wat langer in de zomer). Zo kan de waterdamp, ontstaan tijdens de nacht afgevoerd worden. Ook voor het slapen gaan is het aan te raden even te luchten. Het is niet aan te raden de ramen in de slaapkamer altijd open of op kiepstand te laten staan, zeker in de winter en bij temperaturen onder de 10 graden. Door het afkoelen van de gevel rondom de ramen kunnen er vochtplekken en schimmel ontstaan. Ventilatieroosters kunnen wel continu open staan omdat de koude lucht hierbij niet langs de muren naar binnen stroomt en deze dus minder koud worden. Als u toch graag bij open raam slaapt zorg er in ieder geval voor dat de slaapkamerdeur gesloten is als het raam op kiep staat. Het mee verwarmen van een onverwarmde kamer door het open laten staan van de deur vormt een groot risico voor condensatie op de koude oppervlaktes.<br><br><strong>Extra ventileren</strong>Houd de deur van de badkamer gesloten, zorg wel voor een rooster in de deur of een spleet onder de deur en zet tijdens het douchen de eventuele ventilatie op de hoogste stand, zodat het vocht snel wordt afgevoerd. Ventileer ook extra tijdens het koken, via een open raam of een afzuigkap. En als er op een moment veel mensen in huis zijn, zet dan een deur of raam open. Ook bij klussen in huis zoals schilderen, is extra ventilatie nodig, ook na afloop, dan verdwijnen vrijgekomen stoffen zoals oplosmiddelen sneller uit uw huis.<br><br><strong>Cv-gebruik in de winter</strong>Tijdens koude periodes (lager dan 10 graden) is het van belang de radiatorkranen door het hele huis iets te openen, ook in de ruimtes waar op dat moment niemand aanwezig is. Door het beperkt mee verwarmen van deze ruimtes stookt u over het algemeen niet minder zuinig. Andere vertrekken worden dan ook sneller warm, en u zult minder last hebben van vochtproblemen.<br><br>Meer tips tegen vocht:<ul><li>Kook met de deksel op de pan, dat kost niet alleen minder energie, maar u zorgt er ook voor dat er minder vocht vrijkomt.</li><li>Droog wasgoed het liefst buiten, of in een wasdroger. Hangt u het binnen, doe dit dan in een goed geventileerde ruimte.</li><li>Maak na het dweilen de vloer droog.</li><li>Stop ventilatieroosters nooit dicht, controleer ook regelmatig de ventilatieroosters onderaan in de gevel, die zorgen voor ventilatie van de kruipruimte.</li><li>Gaat u voor langere tijd weg in de winter, laat dan de verwarming ‘op een laag pitje‘ staan ter voorkoming van condens en schimmelproblemen. Het zorgt er ook voor dat uw waterleiding niet bevriest.</li></ul><br><br><h4>Mechanische afzuigventilator</h4>In een huis met een mechanische afzuiging zorgt een ventilator er voortdurend voor dat vervuilde lucht afgevoerd wordt. Tegelijkertijd komt via open ventilatieroosters schone lucht naar binnen. Om altijd een gezond binnenklimaat te kunnen waarborgen zijn deze ventilatiesystemen erop berekend om het hele jaar continu te draaien. Meestal is er een driestanden schakelaar in de woning aanwezig waarmee de installatie geregeld kan worden.<br><br>Oude ventilatoren gebruiken soms nog wisselstroom en verbruiken voor dezelfde prestatie veel meer elektriciteit en maken meer geluid dan moderne gelijkstroom ventilatoren. De besparing op de gebruikte stroom kan oplopen tot ca. 80 %. Een installateur kan direct beoordelen of u nog een wisselstroom ventilator heeft.<br><br><h4>Aandachtspunten voor het juiste gebruik van het ventilatiesysteem</h4>Een drie-standenschakelaar wordt het beste als volgt gebruikt:<ul><li>Stand 1 is de basisstand, bedoeld om het laagste ventilatieniveau te garanderen, bijvoorbeeld als er langere tijd niemand thuis is.</li><li>Stand 2 is de stand die is aanbevolen bij een normale aanwezigheid van mensen in de woning.</li><li>Stand 3 is bedoeld voor afzuiging tijdens koken en vochtafvoer uit de badkamer.</li><li>Vooral wanneer binnenshuis ook natte was wordt opgehangen, is stand 1 echt onvoldoende voor een adequate afvoer van vochtige lucht.</li><li>De verversing van luncht op de slaapkamers is afhankelijk van de aanzuiging in de badkamer. Let er dus op dat een raam in de badkamer alleen kortstondig open blijft staan na het douchen. Als het badkamerraam veel langer open blijft (bijvoorbeeld op een kierstand) is er nauwelijks of geen luchtverversing meer op de overige (slaap-)kamers van dezelfde verdieping. Houdt het badkamerraam bij voorkeur dicht en de badkamerdeur ook. Laat de ventielen en de kieren onder de deuren hun werk doen. Zo verdwijnt alle overtollige vocht snel naar buiten via de ventilatie i.p.v. naar de overige kamers en blijft verversing van de lucht in alle kamers gegarandeerd.</li></ul><br><br><h4>Nieuwe vormen van ventilatiesystemen</h4>Nieuwere ventilatiesystemen kunnen beter geregeld worden. Dit kan bijvoorbeeld op winddruk of met sensoren die continu in de woning of de hoeveelheid vocht en CO<sub>2</sub> meten.<br>Daarbij bestaan er systemen die warmte terug kunnen winnen uit de afgevoerde vervuilde lucht. De terug gewonnen warmte kan gebruikt worden voor het opwarmen van verse binnenkomende lucht of voor het verwarmingssysteem.<br>Er zijn installaties voor de hele woning en apparaten die geschikt zijn voor een enkele ruimte.<br><br>Meer informatie kunt u vinden op onze maatregelbladen hieronder of bij milieucentraal: <a href="https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/ventileren/">https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/ventileren/</a>',

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
                'title' => 'Warmtepomp',
                'description' => 'Een warmtepomp zorgt op een milieuvriendelijke manier voor verwarming van uw huis en warm water in de douche en keuken. Het is een duurzaam alternatief voor uw cv-ketel op gas: uw CO2-uitstoot voor verwarming daalt met zo\'n 50 tot 60 procent! Bovendien kunt u bij aankoop subsidie krijgen en gaat uw energierekening omlaag.<br><br><strong>Wat is een warmtepomp?</strong><br> Een warmtepomp is een onderdeel van een centrale verwarmingsinstallatie en zorgt ervoor dat het verwarmingswater wordt verwarmd en naar de laagtemperatuur verwarmingselementen zoals bijvoorbeeld vloerverwarming wordt gepompt. Meestal zorgt de warmtepomp ook voor warmtapwater, voor o.a. douchen en afwassen. We spreken dan van een combiwarmtepomp. Als de warmtepomp gebruikt wordt naast een cv-ketel die de piekvraag oplost, spreken we van een hybride- warmtepomp. <br><br><strong>Welke varianten zijn er?</strong><br>Warmtepompen zijn in verschillende soorten en maten verkrijgbaar. Belangrijk is welke energiebron wordt toegepast. Dat kan de bodem of de buitenlucht zijn. Het is belangrijk om een warmtepomp te kiezen die past bij uw woning. Hoe groter uw huis, hoe meer capaciteit er nodig is. Bij een combiwarmtepomp is daarnaast de CW-waarde belangrijk. Hoe hoger deze waarde, hoe meer warmtapwater de warmtepomp kan produceren.<br><br><strong>Hoeveel kan ik besparen?</strong><br>De rekenmethodiek voor het berekenen van de kosten en baten binnen het hoomdossier is op dit moment nog in ontwikkeling. Binnenkort kunt u hier terecht voor een indicatie wat een warmtepomp in uw situatie aan besparing op kan leveren.<br><br>Bij vragen over warmtepompen kunt u terecht bij uw coöperatie.',
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
