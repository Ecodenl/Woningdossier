<?php

return [
    'index' => [
        'header' => 'Quick scan vertalingen',
        'table' => [
            'columns' => [
                'name' => 'Vraag',
                'short' => 'short',
                'actions' => 'Acties',
                'edit' => 'Bewerken',
            ],
        ],
    ],
    'edit' => [
        'header' => 'Vraag aanpassen',
        'form' => [
            'name' => 'Naam van vraag',
            'help-text' => 'Help text van vraag',
            'submit' => 'Wijzigingen opslaan',
        ],
    ],
    'update' => [
        'success' => 'Vraag is gewijzigd',
    ],
];
