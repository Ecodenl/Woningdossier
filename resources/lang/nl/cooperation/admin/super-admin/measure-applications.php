<?php

return [
    'index' => [
        'title' => 'Maatregelen beheren',
        'table' => [
            'columns' => [
                'name' => 'Naam',
                'actions' => 'Acties',
            ],
        ],
    ],
    'edit' => [
        'label' => 'Bewerken',
        'title' => 'Maatregel aanpassen',
    ],
    'update' => [
        'success' => 'Maatregel is bijgewerkt',
    ],

    'form' => [
        'measure-name' => [
            'label' => 'Naam',
            'placeholder' => 'Naam van de maatregel',
        ],
        'measure-info' => [
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
