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
        \App\Models\BuildingCoachStatus::STATUS_ACTIVE => 'Actief',

        \App\Models\BuildingCoachStatus::STATUS_PENDING => 'In afwachting',

        \App\Models\BuildingCoachStatus::STATUS_IN_PROGRESS => 'In uitvoering',
        \App\Models\BuildingCoachStatus::STATUS_EXECUTED => 'Uitgevoerd',
        \App\Models\BuildingCoachStatus::STATUS_NO_EXECUTION => 'Geen uitvoering',


        \App\Models\BuildingCoachStatus::STATUS_REMOVED => 'Verwijderd',

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
            'messages' => [
                'send' => 'Versturen'
            ],
            /* translations for the coach environment */
            'coach' => [
                'side-nav' => [
                    'label' => 'Coach menu',
                    'index' => 'Home',
                    'buildings' => 'Mijn woningen',
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
                        'header' => 'Overzicht woningen waaraan ik gekoppeld ben',

                        'table' => [
                            'columns' => [
                                'date' => 'Datum',
                                'name' => 'Naam',
                                'street-house-number' => 'Straat en huisnummer',
                                'zip-code' => 'Postcode',
                                'city' => 'Stadt',
                                'status' => 'Status',
                                'appointment-date' => 'Datum afspraak',
                            ],
                        ],
                    ],

                    'show' => [
                        'save-building-detail' => 'Opmerking opslaan',
                        'header' => 'Detail overzicht :name, :street-and-number, :zipcode-and-city',

                        'observe-building' => [
                            'label' => 'Woning bekijken',
                            'button' => '<i class="glyphicon glyphicon-eye-open"></i>'
                        ],
                        'fill-for-user' => [
                            'label' => 'Woning als coach bewerken',
                            'button' => '<i class="glyphicon glyphicon-edit"></i>'
                        ],
                        'role' => [
                            'label' => 'Rol',
                            'button' => 'Bijwerken'
                        ],
                        'status' => [
                            'label' => 'Kies status: ',
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

                        'set-status' => 'Weet u zeker dat u deze status aan het gebouw wilt hangen?',
                        'set-appointment-date' => 'Weet u zeker dat u de afspraak wilt vastzetten op de gekozen datum?',
                        'set-empty-appointment-date' => 'Weet u zeker dat u de afspraak wilt verwijderen?',
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
                ],
            ],

            /* translations for the coordinator and cooperation-admin */
            'cooperation' => [

                'coaches' => [
                    'index' => [
                        'header' => 'Alle gebruikers van uw coöperatie',

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
                        'header' => '<b>:role:</b> :full_name, :street :number, :zip_code :city',

                        'table' => [
                            'columns' => [
                                'date' => 'Datum',
                                'name' => 'Naam',
                                'street-house-number' => 'Straat en huisnummer',
                                'zip-code' => 'Postcode',
                                'city' => 'Stadt',
                                'status' => 'Status',
                                'appointment-date' => 'Datum afspraak',
                                'no-known-created-at' => 'Niet bekend',
                            ],
                        ],
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
                            'current' => 'Huige status: ',
                            'label' => 'Status: ',
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

                        'set-status' => 'Weet u zeker dat u deze status wilt zetten voor de gekoppelde coaches ?',
                        'set-appointment-date' => 'Weet u zeker dat u deze datum wilt zetten voor de gekoppelde coaches ?',
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
                            'select-coach' => 'Selecteer een coach om te koppelen aan de gebruiker',
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
                    'create' => [
                        'leave-creation-tool' => 'Keer terug naar overzicht',
                        'leave-creation-tool-warning' => 'Let op!, alle wijzigingen zullen verloren gaan. Uw hiervoor gemaakte formulier is dan niet meer terug te krijgen!',
                    ],
                ],
                /* translations specific for the cooperation-admin */
                'cooperation-admin' => [
                    'users' => [
                        'index' => [
                            'header' => 'Overzicht van alle coaches voor uw coöperatie',
                        ],
                    ],
                    'side-nav' => [
                        'label' => 'Coöperatie admin menu',
                        'home' => 'Account overzicht',
                        'coaches' => 'Coaches / coördinatoren',
                        'create-user' => 'Voeg Coach/bewoner toe',
                        'reports' => 'Rapporten',
                        'example-buildings' => 'Voorbeeldwoningen',
                        'questionnaires' => 'Vragenlijsten',
                        'step' => 'Stappen aan en uitzetten',
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
                /* translations specific for the cooperation */
                'coordinator' => [
                    'users' => [
                        'index' => [
                            'header' => 'Overzicht van alle coaches voor uw coöperatie',
                        ],
                    ],
                    'side-nav' => [
                        'label' => 'Coördinator menu',
                        'home' => 'Account overzicht',
                        'coaches' => 'Coaches / coördinatoren',
                        'add-user' => 'Voeg Coach / Bewoner toe',
                        'reports' => 'Rapporten',
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
            'observing-tool' => 'U bekijkt de woning van :first_name :last_name als :input_source_name, u kunt geen aanpassing doen.',
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
