<?php

return [
    'column-translations' => [
        'name' => 'Naam van de token bijv: CRM Api.'
    ],
    'index' => [
        'header' => 'API Tokens van :client_name',
        'token-created' => 'API Token aangemaakt, deze is hieronder eenmalig zichtbaar.',
        'table' => [
            'last-used' => 'Laatste keer gebruikt',
            'name' => 'Naam',
            'edit' => 'Bewerken',
            'actions' => 'Acties',
            'delete' => 'Verwijder API Token',
        ],
    ],
    'create' => [
        'header' => 'API Token aanmaken voor :client_name',
        'submit' => 'Token aanmaken'
    ],
    'edit' => [
        'header' => 'API Token bewerken voor :client_name',
        'submit' => 'Token bijwerken'
    ],
    'update' => [
        'success' => 'API Token en bevoegdheden zijn bijgewerkt',
    ],
    'destroy' => [
        'confirm' => 'Weet je zeker dat je deze token wilt verwijderen?',
        'success' => 'API Token is verwijderd.'
    ],

    'form' => [
        'permissions' => [
            'header' => 'Bevoegdheden',
            'label' => 'Tot welke Coöperaties heeft deze token toegang? (Wanneer dit veld leeg wordt gelaten kan de API token bij elke coöperatie.)',
        ],
    ],
];
