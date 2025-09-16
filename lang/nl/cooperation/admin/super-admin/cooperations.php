<?php

return [
    'index' => [
        'title' => 'Coöperaties',
        'table' => [
            'columns' => [
                'name' => 'Coöperatie naam',
                'slug' => 'Coöperatie Slug / Subdomein',
                'actions' => 'Acties',
            ],
        ],
    ],
    'create' => [
        'title' => 'Coöperatie aanmaken',
    ],
    'store' => [
        'success' => 'Coöperatie aangemaakt',
    ],
    'show' => [
        'title' => 'Details van deze coöperatie'
    ],
    'edit' => [
        'title' => 'Coöperatie bewerken',
    ],
    'update' => [
        'success' => 'Coöperatie bijgewerkt',
    ],
    'destroy' => [
        'confirm' => 'Weet je zeker dat je deze coöperatie wilt verwijderen?',
        'success' => 'Coöperatie verwijderd',
    ],

    'form' => [
        'name' => [
            'label' => 'Naam van de coöperatie',
            'placeholder' => 'Naam van de coöperatie',
        ],
        'slug' => [
            'label' => 'Slug / subdomein',
            'placeholder' => 'Slug / subdomein',
        ],
        'country' => [
            'label' => 'Land',
        ],
        'cooperation-email' => [
            'label' => 'Coöperatie contact e-mailadres',
            'placeholder' => 'Coöperatie contact e-mailadres',
        ],
        'website-url' => [
            'label' => 'Website URL',
            'placeholder' => 'Website URL',
        ],
        'econobis-wildcard' => [
            'label' => 'Econobis Domein Wildcard',
            'placeholder' => 'Hoom',
        ],
        'econobis-api-key' => [
            'label' => 'Econobis API key toevoegen',
            'label-replace' => 'Bestaande Econobis API key overschrijven',
            'clear' => 'API key verwijderen',
        ],
    ],
];
