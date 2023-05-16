<?php

return [
    'column-translations' => [
        'name' => 'Naam van de token bijv: CRM Api.'
    ],
    'index' => [
        'header' => 'API Tokens van :client_name',
        'header-button' => 'API Token aanmaken',
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
        'cooperations' => 'Tot welke Coöperaties heeft dit token toegang ? (Wanneer dit veld word leeg gelaten kan het API token bij elke coöperatie.)',
        'header' => 'API Token aanmaken voor :client_name',
        'submit' => 'Token aanmaken'
    ],
    'edit' => [
        'cooperations' => 'Tot welke Coöperaties heeft dit token toegang ?',
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
];