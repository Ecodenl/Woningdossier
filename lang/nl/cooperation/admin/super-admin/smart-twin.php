<?php

return [
    'index' => [
        'title' => 'SmartTwin bestanden',
        'description' => 'De ruwe resultaten die SmartTwin per woning terugstuurt, en de CSV met de gegevens die de mapping niet heeft kunnen plaatsen. Tijdelijke pagina om de koppeling en de mapping te kunnen testen.',
        'table' => [
            'columns' => [
                'cooperation' => 'Coöperatie',
                'building' => 'Woning',
                'type' => 'Type',
                'flow' => 'Stroom',
                'updated-at' => 'Bijgewerkt',
                'available-until' => 'Beschikbaar tot',
                'actions' => 'Acties',
            ],
            'download' => 'Downloaden',
            'reprocess' => 'Opnieuw verwerken',
            'expired' => 'verlopen',
        ],
    ],
    'reprocess' => [
        'success' => 'De mapping wordt opnieuw uitgevoerd op de opgeslagen resultaten.',
        'not-supported' => 'Dit bestand kan niet opnieuw verwerkt worden.',
        'confirm' => 'De mapping opnieuw uitvoeren op de opgeslagen resultaten van deze woning?',
    ],
];
