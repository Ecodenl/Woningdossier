<?php

return [
    'index' => [
        'title' => 'Gemeenten',
        'table' => [
            'columns' => [
                'name' => 'Naam',
                'bag' => 'BAG gemeente(n)',
                'vbjehuis' => 'VerbeterJeHuis gemeente',
                'actions' => 'Acties',
            ],
        ],
    ],
    'create' => [
        'title' => 'Gemeente aanmaken',
    ],
    'store' => [
        'success' => 'Gemeente aangemaakt',
    ],
    'show' => [
        'title' => 'Gemeente bekijken',
    ],
    'edit' => [
        'title' => 'Gemeente bewerken',
    ],
    'update' => [
        'success' => 'Gemeente bijgewerkt',
    ],
    'destroy' => [
        'success' => 'Gemeente verwijderd',
    ],
    'couple' => [
        'success' => 'Gemeente(n) succesvol gekoppeld',
    ],
    'form' => [
        'name' => [
            'label' => 'Naam',
            'placeholder' => 'Naam van de gemeente',
        ],
        'bag-municipalities' => [
            'label' => 'BAG gemeente(n)',
        ],
        'vbjehuis-municipality' => [
            'label' => 'VerbeterJeHuis gemeente',
        ],
    ],
];
