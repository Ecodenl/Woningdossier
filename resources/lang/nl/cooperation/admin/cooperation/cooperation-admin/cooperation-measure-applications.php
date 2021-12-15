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
        'title' => 'Maatregel aanmaken',
    ],
    'store' => [
        'success' => 'Maatregel succesvol aangemaakt',
    ],
    'edit' => [
        'label' => 'Bewerken',
        'title' => 'Maatregel bewerken',
    ],
    'update' => [
        'success' => 'Maatregel succesvol bijgewerkt',
    ],
    'destroy' => [
        'label' => 'Verwijderen',
        'warning' => 'Weet je zeker dat je deze maatregel wilt verwijderen?',
        'success' => 'Maatregel succesvol verwijderd',
    ],

    'form' => [
        'name' => [
            'label' => 'Naam',
            'placeholder' => 'Naam van de maatregel',
        ],
        'info' => [
            'label' => 'Info',
            'placeholder' => 'Beschrijving van de maatregel',
        ],
        'costs-from' => [
            'label' => 'Investering vanaf',
            'placeholder' => 'Minimale investering van de maatregel',
        ],
        'costs-to' => [
            'label' => 'Investering tot',
            'placeholder' => 'Maximale investering van de maatregel',
        ],
        'savings' => [
            'label' => 'Besparing',
            'placeholder' => 'Besparing van de maatregel',
        ],
        'icon' => [
            'label' => 'Icoon',
        ],
    ],
];