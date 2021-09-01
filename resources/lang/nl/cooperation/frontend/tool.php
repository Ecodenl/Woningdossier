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
        'title' => 'Uw geadviseerde Woonplan',
        'help' => 'Wilt u iets aanpassen? Sleep dan de maatregelen naar de gewenste kolom',
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
    ],
];