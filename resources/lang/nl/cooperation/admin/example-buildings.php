<?php

return [
    'components' => [
        'cooperation' => 'Coöperatie',
        'order' => 'Volgorde',
        'is-default' => [
            'label' => 'Standaard waarde',
            'options' => [
                0 => 'Nee',
                1 => 'Ja',
            ],
        ],
        'contents' => [
            'title' => 'Bouw jaren',
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
        'title' => 'Aan het bewerken: :name',
        'new-warning' => 'Deze data wordt alleen opgeslagen als dit tabblad open is wanneer op "opslaan" geklikt wordt.',
    ],
    'form' => [
        'build-year' => 'Bouwjaar',
        'field-name' => 'Veldnaam',
        'field-value' => 'Waarde',
        'update' => 'Opslaan',
        'general-data' => 'Algemene gegevens',
        'interest-in-measure' => 'Interesse in :item',
        'is-considering' => 'Meenemen in berekening',
    ],
    'store' => [
        'success' => 'Voorbeeldwoning toegevoegd',
    ],
    'update' => [
        'success' => 'Voorbeeldwoning bijgewerkt',
    ],
    'destroy' => [
        'confirm' => 'Weet je zeker dat je deze voorbeeldwoning wilt verwijderen?',
        'success' => 'Voorbeeldwoning verwijderd',
    ],
];
