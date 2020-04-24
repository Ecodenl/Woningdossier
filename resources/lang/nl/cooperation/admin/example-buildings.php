<?php

return [
    'components' => [
        'cooperation' => 'Coöperatie',
        'order' => 'Volgorde',
        'is-default' => [
            'label' => 'Standaard waarde',
            'options' => [
                0 => 'Nee',
                1 => 'Ja'
            ]
        ],
        'contents' => [
            'title' => 'Bouw jaren'
        ],
    ],
    'index' => [
        'create-button' => 'Nieuwe toevoegen',
        'table' => [
            'name' => 'Naam',
            'order' => 'Volgorde',
            'cooperation' => 'Coöperatie',
            'default' => 'Standaard',
            'actions' => 'Acties',
        ],
    ],
    'edit' => [
        'title' => 'Aan het bewerken: :name'
    ],
    'form' => [
        'build-year' => 'Bouwjaar',
        'field-name' => 'Veldnaam',
        'field-value' => 'Waarde',
        'update' => 'Opslaan'
    ],
    'store' => [
        'success' => 'Voorbeeld woning toegevoegd'
    ],
    'update' => [
        'success' => 'Voorbeeld woning bijgewerkt'
    ]
];