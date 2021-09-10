<?php

return [
    'step-count' => 'Stap :current van :total',
    'no-answer-given' => 'Geen antwoord ingevuld',

    'form' => [
        'subject' => 'Onderwerp',
        'other' => 'Anders',
        'add-option' => 'Voeg onderdeel toe',

        'questions' => [
            'values' => [
                'more-than' => 'Meer dan :value jaar',
            ],
        ],
    ],

    'my-plan' => [
        'label' => 'Woonplan',
        'title' => [
            'quick-scan' => 'Uw quickscan Woonplan',
            'expert' => 'Uw Woonplan',
        ],
        'help' => 'Wilt u iets aanpassen? Sleep dan de maatregelen naar de gewenste kolom',
        'info' => [
            'quick-scan' => '<p>Voor een gedetailleerd kunt u de verdiepingsvragen invullen. U kunt ook de hulp van een <a href=":link">energiecoach</a> inroepen.</p>',
            'expert' => '<p>U kunt hulp van een <a href=":link">energiecoach</a> aanvragen voor extra advies</p>',
        ],
        'categories' => [
            \App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE => 'In orde',
            \App\Services\UserActionPlanAdviceService::CATEGORY_TO_DO => 'Nu aanpakken',
            \App\Services\UserActionPlanAdviceService::CATEGORY_LATER => 'Later uitvoeren',
        ],
        'cards' => [
            'see-info' => 'Zie info',
            'subsidy' => [
                // Todo when constants are available
            ],
            'investment' => 'Investering',
            'savings' => 'Besparing per jaar',
        ],
        'comments' => [
            'resident' => 'Opmerkingen bewoner',
            'coach' => 'Opmerkingen coach',
        ],
        'loading' => 'Woonplan wordt berekend...',
    ],
];