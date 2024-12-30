<?php

return [
    'index' => [
        'header' => 'Stappen waarvan u de vragen kunt vertalen',
        'text' => 'Hier zijn alle stappen te zien waarvan u vragen en bijbehorende helpteksten kunt aanpassen',
        'table' => [
            'columns' => [
                'name' => 'Stap naam',
                'actions' => 'Acties',
            ],
            'pdf' => 'PDF Vertalingen',
            'main-translations' => 'Herhalende teksten',
            'see' => 'Bekijk vertalingen',
        ],
    ],
    'edit' => [
        'header' => 'Alle vragen die vertaalbaar zijn op de stap :step_name',
        'question' => 'Vraag in taal: :locale',
        'help' => 'Helptext in taal: :locale',
        'search' => [
            'placeholder' => 'Zoek naar een vraag...',
        ],
        'save' => 'Wijzigingen voor de vragen, sub-vragen en helpteksten opslaan.',
        'sub-questions' => 'Sub-vragen',
    ],
    'update' => [
        'success' => 'Vertalingen zijn bijgewerkt.',
    ],
];
