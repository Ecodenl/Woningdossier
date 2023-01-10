<?php

return [
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
                'no-known-created-at' => 'Niet bekend',
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
];