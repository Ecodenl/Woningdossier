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
    'log-messages' => [
        'logged-in' => ':full_name heeft ingelogd op de applicatie',
        'registered-user' => ':full_name heeft geregistreerd',
        'user-associated-with-other-cooperation' => ':full_name heeft zich gekoppeld aan de coöperatie :cooperation_name',
        'step-data-has-been-changed' => ':full_name heeft een wijziging doorgevoerd in het actieplan',
        'filling-tool-for' => ':full_name is ingelogd op de tool om gegevens aan te passen van :for_full_name',
        'observing-tool-for' => ':full_name is ingelogd op de tool om de gegevens te bekijken van :for_full_name',
        'action-plan-changed' => ':full_name heeft een wijziging doorgevoerd op het actieplan',
        'participant-added' => ':full_name heeft :for_full_name gekoppeld aan deze woning',
        'user-gave-access' => ':full_name heeft toegang gegeven tot zijn woning',
        'user-revoked-access' => ':full_name heeft de toegang ingetrokken voor zijn woning'
    ],
    'building-coach-statuses' => [
        \App\Models\BuildingCoachStatus::STATUS_REMOVED => 'Verwijderd',
        \App\Models\BuildingCoachStatus::STATUS_ADDED => 'Toegevoegd',

    ],
    'cooperation' => [
        'create-building' => [
            'current-login-info' => [
                'header' => 'Voer uw e-mail in ter controlle.',
            ],
            'building' => [
                'header' => 'Vul uw adres in.',
                'store' => 'Adres aanmaken',
            ],
            'store' => [
                'success' => 'Uw adres is toegevoegd aan het Hoomdossier, u kunt nu inloggen.',
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
                'revoke-access' => 'Weet u zeker dat u deze gebruiker van uit het groepsgesprek wilt verwijderen? Deze gebruiker heeft hierna geen toegang meer tot de woning.',
            ],
            'messages' => [
                'participant-added' => ':participant is toegevoegd aan het gesprek',
                'participant-removed' => ':participant is verwijderd uit het gesprek',
            ],
        ],

        'navbar' => [
            'my-account' => 'Mijn account',
            'disclaimer' => 'Disclaimer',
            'privacy' => 'Privacy',
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
                    'users' => 'Gebruikers filteren',
                    'key-figures' => 'Kengetallen',
                    'translations' => 'Vertalingen',
                    'example-buildings' => 'Voorbeeldwoningen',
                ],
                'index' => [
                    'header' => 'Super admin panel',
                    'text' => 'Beheer de applicatie',
                    'cooperations' => 'Cooperaties',
                    'users' => 'Gebruikers',
                    'buildings' => 'Woningen',
                ],
                'key-figures' => [
                    'index' => [
                        'header' => 'Kengetallen',
                        'sections' => [
                            'general' => 'Algemeen',
                            'measure_applications' => 'Maatregelen'
                        ],
                        'table' => [
                            'title' => 'Kengetal naam / type',
                            'key-figure' => 'Waarde kengetal',
                            'key-figure-unit' => 'Eenheid',
                            'measure_applications' => [
                                'measure-type' => 'Maatregel type',
                                'measure-name' => 'Maatregel naam',
                                'application' => 'Toepassing',
                                'costs' => 'Kosten',
                                'cost-unit' => 'Kosten per',
                                'minimal-costs' => 'Minimale kosten',
                                'maintenance-interval' => 'Onderhoudsinterval',
                                'maintenance-unit' => 'Onderhoud per'
                            ],
                        ],
                    ],
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
                        'modal' => [
                            'text' => 'U staat op het punt om de coöperatie :cooperation te verwijderen, hiermee word alles verwijderd wat aan de coöperatie hangt (gebruikers, vragenlijsten, berichten etc.).',
                            'cancel' => 'Annuleren',
                            'destroy' => 'Ja, ik wil deze coöperatie verwijderen'
                        ],
                        'edit' => 'Bewerken',
                        'show' => 'Details van deze coöperatie',
                        'create' => 'Aanmaken',
                        'destroy' => 'Verwijder coöperatie'
                    ],
                    'create' => [
                        'header' => 'Cooperatie toevoegen',
                        'form' => [
                            'name' => 'Naam van de coöperatie *',
                            'slug' => 'Slug / subdomein *',
                            'cooperation_email' => 'Coöperatie contact e-mailadres',
                            'website_url' => 'Website URL',
                            'create' => 'Aanmaken',
                        ],
                    ],
                    'edit' => [
                        'header' => 'Cooperatie :name bewerken',
                        'form' => [
                            'name' => 'Naam van de coöperatie *',
                            'slug' => 'Slug / subdomein *',
                            'cooperation_email' => 'Coöperatie contact e-mailadres',
                            'website_url' => 'Website URL',
                            'update' => 'Bijwerken',
                        ],
                    ],
                    'destroy' => [
                        'success' => 'De coöperatie is succesvol verwijderd'
                    ],
                    'store' => [
                        'success' => 'Cooperatie is toegevoegd aan het Hoomdossier',
                    ],
                    'update' => [
                        'success' => 'Cooperatie is bijgewerkt.',
                    ],
                    'cooperation-to-manage' => [
                        'alert-on-top' => 'Alle wijzigingen die u nu doorvoert zullen worden gedaan op de coöperatie <strong>:cooperation</strong>',
                        'back-to-normal-environment' => 'Keer terug naar de admin omgeving',
                        'side-nav' => [
                            'label' => ':cooperation_name menu',
                            'home' => 'Home',
                            'coordinator' => 'Coordinatoren',
                            'cooperation-admin' => 'Cooperatie admins',
                            'users' => 'Alle gebruikers',
//                            'promote-user' => ''
                        ],
                        'cooperation-admin' => [
                            'index' => [
                                'header' => 'Overzicht van alle coöperatie admins',
                                'table' => [
                                    'name' => 'Naam',
                                    'email' => 'E-mail',
                                    'actions' => 'Acties'
                                ]
                            ]
                        ],
                        'coordinator' => [
                            'index' => [
                                'header' => 'Overzicht van alle coordinatoren',
                                'table' => [
                                    'name' => 'Naam',
                                    'email' => 'E-mail',
                                ]
                            ]
                        ],
                        'users' => [
                            'index' => [
                                'header' => 'Overzicht van alle gebruikers',
                                'table' => [
                                    'created-at' => 'Datum',
                                    'name' => 'Naam',
                                    'email' => 'E-mail',
                                    'actions' => 'Acties',
                                ]
                            ],
                            'show' => [
                                'header' => 'Overzicht van de gebruiker :name',
                                'role' => [
                                    'label' => 'Rol',
                                    'alert' => 'Weet u zeker dat u deze rol wilt toepassen ?'
                                ],
                            ]
                        ],
                        'home' => [
                            'index' => [
                                'header' => 'Overzicht van de coöperatie :cooperation',
                                'coach-count' => 'Coaches',
                                'resident-count' => 'Bewoners',
                                'coordinator-count' => 'Coordinatoren',
                            ]
                        ],
                    ],
                ],
            ],
            'navbar' => [
                'current-role' => 'Uw huidige rol:',
                'reports' => 'Rapportages',
                'example-buildings' => 'Voorbeeldwoningen',
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
            'users' => [
                'show' => [
                    'header' => 'Detail overzicht :name, :street-and-number, :zipcode-and-city, :email, :phone-number',

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
                        'no' => 'Geen toegang tot woning',
                        'yes' => 'Toegang tot woning'
                    ],

                    'delete-user' => 'Weet u zeker dat u deze gebruiker wilt verwijderen, deze actie kan niet ongedaan worden gemaakt',
                    'revoke-access' => 'Weet u zeker dat u deze gebruiker van de van groeps-chat wilt verwijderen, de gebruiker heeft hierna geen toegang meer tot de woning.',
                    'add-with-building-access' => 'Weet u zeker dat u deze gebruiker aan de groeps-chat toegang wilt geven ? De gebruiker heeft hierna ook toegang tot de woning',

                    'set-status' => 'Weet u zeker dat u deze status wilt zetten voor het huidige gebouw?',
                    'set-appointment-date' => 'Weet u zeker dat u deze datum wilt zetten voor het huidige gebouw?',
                    'set-empty-appointment-date' => 'Weet u zeker dat u de afspraak wilt verwijderen?',
                    'give-role' => 'Weet u zeker dat u deze gebruiker de rol wilt geven?',
                    'remove-role' => 'Weet u zeker dat u de rol wilt intrekken van deze gebruiker?',

                    'tabs' => [
                        'messages-public' => [
                            'user-notification' => [
                                'yes' => 'Gebruiker heeft notificaties aanstaan, hij zal op de hoogte worden gesteld van de verstuurde berichten.',
                                'no' => 'Gebruiker ontvangt hier geen melding van.'
                            ],
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
                    'text' => 'Alle woningen waar u toegang tot heeft, u kunt op de pagina voor woningen acties uitvoeren hierop.',

                    'table' => [
                        'columns' => [
                            'street' => 'Straatnaam',
                            'city' => 'Stad',
                            'owner' => 'Eigenaar',
                            'actions' => 'Acties',
                        ],
                    ],
                ],

                'messages' => [
                    'index' => [
                        'header' => 'Overzicht van woningen waar u contact mee kunt opnemen',

                        'table' => [
                            'columns' => [
                                'most-recent-message-date' => 'Meest recente ontvangst datum',
                                'street-house-number' => 'Straat en huisnummer',
                                'zip-code' => 'Postcode',
                                'city' => 'Stad',
                                'unread-messages' => 'Aantal ongelezen berichten'
                            ],
                        ],
                    ]
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
                                'city' => 'Stad',
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
                            'no' => 'Geen toegang tot woning',
                            'yes' => 'Toegang tot woning'
                        ],

                        'set-status' => 'Weet u zeker dat u deze status aan de woning wilt hangen?',
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
                                'title' => 'Opmerkingen bij woning',
                                'note' => 'Opmerking',
                                'save' => 'Opmerking opslaan',
                            ],
                            'fill-in-history' => [
                                'title' => 'Invulhistorie',
                                'table' => [
                                    'columns' => [
                                        'user' => 'Gebruiker die de actie heeft gedaan',
                                        'building' => 'Actie voor woning',
                                        'for-user' => 'Actie op gebruiker',
                                        'message' => 'Bericht',
                                        'happened-on' => 'Gebeurt op'
                                    ]
                                ]
                            ]
                        ],
                        'next' => 'Volgende',
                        'previous' => 'Vorige'
                    ],
                ],
            ],

            /* translations for the coordinator and cooperation-admin */
            'cooperation' => [

                'messages' => [
                    'index' => [
                        'header' => 'Overzicht van woningen waar een conversatie is',

                        'table' => [
                            'columns' => [
                                'most-recent-message-date' => 'Meest recente ontvangst datum',
                                'sender-name' => 'Verzender',
                                'street-house-number' => 'Straat en huisnummer',
                                'zip-code' => 'Postcode',
                                'city' => 'Stad',
                                'unread-messages' => 'Aantal ongelezen berichten'
                            ],
                        ],
                    ]
                ],

                'coaches' => [
                    'index' => [
                        'header' => 'Alle gebruikers van uw coöperatie',

                        'table' => [
                            'columns' => [
                                'date' => 'Datum',
                                'name' => 'Naam',
                                'street-house-number' => 'Straat en huisnummer',
                                'zip-code' => 'Postcode',
                                'city' => 'Stad',
                                'email' => 'Email',
                                'roles' => 'Huidige rollen'
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
                                'city' => 'Stad',
                                'status' => 'Status',
                                'appointment-date' => 'Datum afspraak',
                                'no-known-created-at' => 'Niet bekend',
                            ],
                        ],
                    ],
                ],
                'reports' => [
                    'index' => [

                        'title' => 'Rapportages',
                        'description' => 'Rapportage downloads',


                        'table' => [
                            'columns' => [
                                'name' => 'Rapport type',
                                'download' => 'Download file',
                                'available-report' => 'Beschikbaar rapport'
                            ],
                            'report-in-queue' => 'Het rapport wordt gegenereerd',
                            'generate-btn' => 'Genereer'
                        ],
                    ],

                    'generate' => [
                        'success' => 'Het rapport wordt gemaakt',
                    ],
                    'csv-columns' => [
                        'input-source' => 'Rol',
                        'created-at' => 'Datum account',
                        'status' => 'Status',
                        'allow-access' => 'Toestemming aanwezig',
                        'associated-coaches' => 'Gekoppelde coaches',
                        'first-name' => 'Voornaam',
                        'last-name' => 'Achternaam',
                        'email' => 'Email',
                        'phonenumber' => 'Telefoonnummer',
                        'mobilenumber' => 'Mobiel nummer',
                        'street' => 'Straat',
                        'house-number' => 'Huis nummer',
                        'zip-code' => 'Postcode',
                        'city' => 'Woonplaats',
                        'building-type' => 'Woningtype',
                        'build-year' => 'Bouwjaar',
                        'example-building' => 'Specifieke voorbeeldwoning',
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
                                'city' => 'Stad',
                                'status' => 'Status',
                                'no-known-created-at' => 'Niet bekend'
                            ],
                        ],
                    ],


                    'create' => [
                        'form' => [
                            'already-member' => 'De gebruiker met dit e-mailadres is al actief bij deze coöperatie!',
                            'e-mail-exists' => 'Er is al een account met dit e-mailadres. Indien u doorgaat wordt dit account aan uw cooperatie gekoppeld met de rollen die u opgeeft.',
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
                        'private-message-allowed-access' => 'U heeft de coöperatie toegang gegeven tot uw Hoomdossier.',
                        'success' => 'Gebruiker is toevoegd!',
                    ],
                    'destroy' => [
                        'warning' => 'Let op: dit verwijdert de gebruiker en al zijn gegevens die zijn opgeslagen in het Hoomdossier. Weet u zeker dat u wilt doorgaan?',
                        'success' => 'Gebruiker is verwijderd',
                    ],
                ],
                'questionnaires' => [
                    'index' => [
                        'header' => 'Alle vragenlijsten voor uw coöperatie',
                        'table' => [
                            'columns' => [
                                'questionnaire-name' => 'Vragenlijst naam',
                                'step' => 'Komt na stap',
                                'active' => 'Actief',
                                'actions' => 'Acties',
                                'see-results' => 'Bekijk resultaten',
                                'edit' => 'Bewerk vragenlijst',
                                'destroy' => 'Verwijder vragenlijst'
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
                    'destroy' => [
                        'are-you-sure' => 'Dit verwijderd de vragenlijst, vragen en de gegeven antwoorden. Weet u zeker dat u wilt doorgaan ?',
                        'success' => 'Vragenlijst verwijderd',
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
                        'create-user' => 'Gebruiker toevoegen',
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
                        'add-user' => 'Gebruiker toevoegen',
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
                'meters' => 'm',
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
                'title' => 'Gevelisolatie',
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

            'high-efficiency-boiler' => [
                'title' => 'HR CV Ketel',
            ],

            'boiler' => [
                'title' => 'HR CV Ketel',
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
