<?php

return [
    'index' => [
        'title' => 'SmartTwin maatregelen',
        'description' => 'SmartTwin adviseert een product, Hoomdossier een maatregel. Hier leg je vast welke maatregel een product is. Een product zonder keuze komt niet in het woonplan terecht en wordt als niet-gemapt gerapporteerd.',
        'empty' => 'De catalogus is nog niet ingelezen. Draai eerst `php artisan api:smarttwin:import-solutions`.',
        'undecided' => '{0} Alle producten zijn beoordeeld.|{1} Nog 1 product zonder keuze.|[2,*] Nog :count producten zonder keuze.',
        'table' => [
            'no-kind' => 'Zonder soort',
            'bulk' => 'Hele soort instellen op…',
            'undecided' => 'Nog niet beoordeeld',
            'not-coupled' => 'Niet koppelen',
            'withdrawn' => '(niet meer in de catalogus)',
        ],
    ],
    'couple' => [
        'success' => '{0} Er is niets gewijzigd.|{1} 1 koppeling opgeslagen.|[2,*] :count koppelingen opgeslagen.',
    ],
];
