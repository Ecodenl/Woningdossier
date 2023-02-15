<?php

use App\Models\InputSource;
use App\Services\Verbeterjehuis\RegulationService;

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
            'quick-scan' => '<p>Voor een gedetailleerd advies, kunt u de verdiepingsvragen invullen. U kunt ook de hulp van een <a href=":link">energiecoach</a> inroepen.</p>',
            'expert' => '<p>U kunt hulp van een <a href=":link">energiecoach</a> aanvragen voor extra advies</p>',
        ],
        'categories' => [
            \App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE => 'In orde',
            \App\Services\UserActionPlanAdviceService::CATEGORY_TO_DO => 'Nu aanpakken',
            \App\Services\UserActionPlanAdviceService::CATEGORY_LATER => 'Later uitvoeren',
        ],
        'cards' => [
            'add-advices' => [
                'header' => 'Selecteer een optie',
                'options' => [
                    'trashed' => [
                        'button' => 'Verwijderde maatregel terugzetten',
                        'title' => 'Maatregelen toevoegen',
                        'help' => 'Klik op een maatregel om deze weer toe te voegen aan het woonplan.',
                    ],
                    'expert' => [
                        'button' => 'Standaard maatregel toevoegen',
                        'title' => 'Naar een verdiepingsvraag',
                        'help' => 'Ga hier verder naar een verdiepingsvraag.',
                    ],
                    'add' => [
                        'button' => 'Eigen maatregel toevoegen',
                    ],
                ],



            ],
            'see-info' => 'Zie info',
            'subsidy' => [
                // Todo when constants are available
            ],
            'investment' => 'Investering',
            'savings' => 'Besparing per jaar',
        ],
        'comments' => [
            InputSource::RESIDENT_SHORT => 'Opmerkingen bewoner',
            InputSource::COACH_SHORT => 'Opmerkingen coach',
        ],

        'loading' => 'Woonplan wordt berekend...',

        'calculations' => [
            'title' => 'Uitleg berekeningen',
            'table' => [
                'info' => 'Vraag / info',
                'value' => 'Waarde',
                'source' => 'Oorsprong',
            ],
            'description' => "<p>Voor het berekenen van de prijzen maken wij gebruik van de 'Kostenkentallen energiebesparende maatregelen' van RVO. Deze online database kun je via de volgende link vinden: <a target='_blank' rel='nofollow' href='https://digipesis.com/'>Kostenkentallen | RVO</a></p>",
            'values' => [
                'gas-cost' => 'Gerekend met kosten voor gas',
                'electricity-cost' => 'Gerekend met kosten voor elektriciteit',
            ],
        ],

        'uploader' => [
            'add' => 'Bestanden toevoegen',
            'view' => 'Bestanden bekijken',
            'help' => 'Klik op een bestand om deze te bewerken.',
            'info' => [
                'title' => 'Informatie',
                'uploaded-by' => 'Geüpload door',
                'created-at' => 'Aangemaakt op',
                'type' => 'Type bestand',
            ],
            'form' => [
                'header' => 'Bestand bewerken',
                'header-view' => 'Bestand bekijken',
                'title' => [
                    'label' => 'Titel',
                ],
                'description' => [
                    'label' => 'Beschrijving',
                ],
                'tag' => [
                    'label' => 'Type bestand',
                ],
                'share-with-cooperation' => [
                    'label' => 'Met cooperatie delen',
                    'options' => [
                        'show' => 'Zichtbaar voor cooperatie',
                        'hide' => 'Niet zichtbaar voor cooperatie',
                    ],
                ],
                'download' => [
                    'title' => 'Downloaden',
                ],
                'delete' => [
                    'title' => 'Verwijderen',
                    'confirm' => 'Weet je zeker dat je dit bestand wilt verwijderen?',
                ],
            ],
        ],
        'downloads' => [
            'file-is-processing' => 'Rapportage wordt gemaakt..',
            'download-report' => 'Download bestaande rapportage',
            'create-report' => 'Maak rapportage',
        ],
    ],
    'my-regulations' => [
        'provider' => [
            'to' => 'Naar aanbieder',
        ],
        'categories' => [
            RegulationService::SUBSIDY => 'Subsidies (:count)',
            RegulationService::LOAN => 'Leningen (:count)',
            RegulationService::OTHER => 'Overige (:count)',
        ],
        'container' => [
            'intro' => [
                RegulationService::SUBSIDY => 'Er zijn de volgende subsidieregelingen beschikbaar voor jouw situatie:',
                RegulationService::LOAN => 'Er zijn de volgende leningen beschikbaar in jouw gemeente:',
                RegulationService::OTHER => 'Andere beschikbare regelingen:',
            ],
        ]
    ],
];
