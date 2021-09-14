<?php

return [
    'index' => [
        'title' => 'Maatregelen beheren',

        'table' => [
            'columns' => [
                'name' => 'Naam',
                'icon' => 'Icoon',
                'actions' => 'Acties',
            ],
        ],
    ],
    'create' => [
        'label' => 'Aanmaken',
    ],
    'store' => [
        'success' => 'Maatregel succesvol aangemaakt',
    ],
    'edit' => [
        'label' => 'Bewerken',
    ],
    'update' => [
        'success' => 'Maatregel succesvol bijgewerkt',
    ],
    'destroy' => [
        'label' => 'Verwijderen',
        'warning' => 'Weet je zeker dat je deze maatregel wilt verwijderen?',
        'success' => 'Maatregel succesvol verwijderd',
    ],
];