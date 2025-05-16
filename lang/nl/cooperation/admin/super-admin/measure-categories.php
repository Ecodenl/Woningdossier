<?php

return [
    'index' => [
        'title' => 'Maatregel categorieeën',
        'table' => [
            'columns' => [
                'name' => 'Naam',
                'actions' => 'Acties',
            ],
        ],
    ],
    'create' => [
        'title' => 'Maatregel categorie aanmaken',
    ],
    'store' => [
        'success' => 'Maatregel categorie aangemaakt',
    ],
    'show' => [
        'title' => 'Maatregel categorie bekijken',
    ],
    'edit' => [
        'title' => 'Maatregel categorie bewerken',
    ],
    'update' => [
        'success' => 'Maatregel categorie bijgewerkt',
    ],
    'destroy' => [
        'success' => 'Maatregel categorie verwijderd',
    ],
    'form' => [
        'name' => [
            'label' => 'Naam',
            'placeholder' => 'Naam van de maatregel categorie',
        ],
        'vbjehuis-measure' => [
            'label' => 'VerbeterJeHuis maatregel',
            'option' => [
                'none' => 'Geen maatregel koppelen',
            ],
        ],
    ],
];
