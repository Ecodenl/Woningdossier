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
            // On a card of the package SmartTwin calculated, in whichever column it is.
            'package-label' => 'Verbeterpakket',
            'regulations' => [
                'subsidy-available' => 'Subsidie mogelijk',
                'after-subsidy-cut' => 'Na aftrek subsidie',
                'loan-available' => 'Lening mogelijk',
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
        // The woonplan before there is one: the resident still has to do the check in SmartTwin.
        'start-check' => [
            'body' => '<p>Waar begin je met het verduurzamen van je huis?</p>
<p>Vul deze verbetercheck in en ontdek alvast welke maatregelen voor jou interessant zijn. Samen met je energiecoach kun je straks een passend woonplan maken.</p>',
        ],

        // The check is done and SmartTwin is working on the advice.
        'awaiting-advice' => [
            'body' => '<p>Je verbetercheck is binnen. We stellen je woonplan samen.</p>
<p>Dit duurt meestal niet lang. Zodra het klaar is kom je hier vanzelf terecht.</p>',
        ],

        // The woonplan while a coach works on it in SmartTwin (woonplan design "Processen woonplan
        // bewoner", "Teksten op woonplanpagina bij vergrendeling door coach").
        'lock' => [
            'awaiting' => [
                'title' => 'Nog even geduld.',
                'text' => 'Jouw energiecoach werkt aan jouw woonplan. Zodra deze gereed is, kun je hier zien welke maatregelen voor jou het meest geschikt zijn, hoeveel je ermee kunt besparen en welke kosten en subsidies je kunt verwachten. Heb je vragen? Neem dan contact op met jouw coach.',
            ],
            'locked' => [
                'title' => 'Jouw woonplan',
                'text' => 'Hier is jouw woonplan zoals je die met de energiecoach hebt besproken. Om ongewenste wijzigingen te voorkomen, is hij vergrendeld totdat de energiecoach klaar is. Later kun je zelf weer aanpassingen doen, bijvoorbeeld aangeven dat je een maatregel hebt uitgevoerd.',
            ],
            'done' => [
                'title' => 'Jouw woonplan',
                'text' => 'Hier is jouw woonplan zoals je die met de energiecoach hebt besproken. Als je wilt kun je nu zelf aanpassingen doen, bijvoorbeeld aangeven dat je een maatregel hebt uitgevoerd.',
            ],
        ],
        // "Maatregel uitgevoerd" (woonplan design "Processen woonplan bewoner").
        'executed' => [
            'button' => 'Uitgevoerd',
            'title' => ':measure uitgevoerd',
            'date' => 'Datum uitvoering',
            'description' => 'Omschrijving',
            'documents' => 'Documenten uploaden',
            'documents-help' => 'Bijvoorbeeld de factuur. Je vindt ze daarna ook bij je bestanden.',
            'save' => 'Opslaan',
            'cancel' => 'Annuleren',
            'undo' => 'Toch niet uitgevoerd',
            'edit' => 'Bekijken of wijzigen',
            'executed-on' => 'Uitgevoerd op :date',
            // An executed measure that a new SmartTwin calculation put back in the package.
            'advised-again' => 'Uitgevoerd op :date, maar opnieuw geadviseerd: pas je woning aan in SmartTwin',
        ],
        // "Gekozen pakket" under the columns of a SmartTwin woonplan (woonplan design "Pagina 2").
        'package-results' => [
            'title' => 'Gekozen pakket',
            'costs' => 'Kosten',
            'total' => 'Totaal pakket',
            'no-costs' => 'Geen kosten bekend',
            'savings' => 'Besparing',
            'per-year' => 'per jaar',
            'subsidy' => 'Subsidie',
            'energy-label' => 'Energielabel',
            'gas' => 'Gasverbruik',
            'electricity' => 'Stroomverbruik',
            'co2-savings' => 'CO2-besparing',
            'broken' => 'Verbeterpakket aangepast,',
            'broken-link' => 'klik hier om een nieuwe berekening te maken.',
            'adjust' => 'Maatregelpakket aanpassen',
            'adjust-warning' => "Let op: bij een herberekening worden alle maatregelen in de kolommen 'Nu aanpakken' en 'Later uitvoeren' gereset!",
            'adjust-confirm' => 'Verder naar SmartTwin',
            'adjust-cancel' => 'Annuleren',
        ],
        'smarttwin' => [
            'button' => 'Doe de verbetercheck',
            'redirecting' => 'Je wordt ingelogd bij SmartTwin…',
            'noscript' => 'Klik op de knop om verder te gaan naar SmartTwin.',
            'continue' => 'Verder naar SmartTwin',
            'errors' => [
                'not_configured' => 'Er is nog geen SmartTwin-account voor je beschikbaar. Probeer het later opnieuw.',
                'advice_in_progress' => 'Het adviesgesprek voor dit adres is op dit moment door een andere gebruiker geopend. Probeer het later opnieuw.',
                'unsupported_role' => 'SmartTwin is niet beschikbaar voor je huidige rol.',
                'failed' => 'Er ging iets mis bij het openen van SmartTwin. Probeer het later opnieuw.',
            ],
        ],
    ],
    'my-regulations' => [
        'loading' => 'Maatregelen worden berekend...',
        'provider' => [
            'to' => 'Naar aanbieder',
        ],
        'refreshed' => [
            'not' => 'Regelingen zijn nog niet opgehaald',
            'last' => 'Laatst geupdate om :date',
            'busy' => 'Wordt ververst..',
            'ready' => 'Ververs regelingen',
            'not-available' => 'Het is niet mogelijk uw regelingen te verversen',
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
