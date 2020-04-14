<?php

return [
    'index' => [
        'header' => 'Alle vragenlijsten voor uw coöperatie',
        'table' => [
            'columns' => [
                'questionnaire-name' => 'Vragenlijst naam',
                'step' => 'Komt na stap',
                'actions' => 'Acties',
                'copy' => 'Kopiëren',
                'edit' => 'Bewerk vragenlijst',
            ],
        ],
    ],
    'edit' => [
        'header' => 'Vragenlijst kopiëren naar andere coöperaties',
        'form' => [
            'questionnaire' => 'Vragenlijst om te kopiëren',
            'cooperations' => 'Vragenlijst naar de volgende coöperaties kopiëren',
            'submit' => 'Vragenlijst kopiëren'
        ]
    ],
    'copy' => [
        'success' => 'Vragenlijst is gekopieerd naar de coöperaties.'
    ],
];