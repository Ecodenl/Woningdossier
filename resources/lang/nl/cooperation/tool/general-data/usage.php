<?php

return [
    'index' => [
        'water-gas' => [
            'title' => [
                'title' => 'Warm tapwater en koken'
            ],
            'cook-gas' => [
                'title' => 'Wordt er op gas gekookt?',
                'help' => 'Wordt er op gas gekookt?',
            ],
            'water-comfort' => [
                'title' => 'Wat is het comfortniveau voor het gebruik van warm tapwater? ',
                'help' => '
                                <p>Het gaat om het gebruik van warm water voor keuken, douche en bad.</p>
<p><strong>Standaard</strong> = kort douchen met waterbesparende douchekop;</p>
<p><strong>Comfort</strong> = 5 - 10 minuten douchen en/of geen waterbesparende douchekop; af en toe badgebruik;</p>
<p><strong>Comfort plus</strong> = meer dan 10 minuten douchen en of regendouche / speciale luxe douche; Regelmatig badgebruik is altijd categorie comfort plus</p>

            '
            ],
            'resident-count' => [
                'title' => 'Wat is het aantal bewoners?',
                'help' => 'Hoeveel personen wonen er in de woning. Logees die alleen gedurende een aantal weken of maanden in huis wonen tellen niet mee.'
            ],
        ],

        'energy-usage' => [
            'title' => [
                'title' => 'Energiegebruik',
            ],
            'gas-usage' => [
                'title' => 'Wat is het gasgebruik van afgelopen jaar? (in m3 gas per jaar)',
                'help' => 'U kunt dit vinden in de jaarafrekening van uw energieleverancier, opgave over een heel jaar.'
            ],
            'amount-electricity' => [
                'title' => 'Wat is het elektragebruik van het afgelopen jaar?',
                'help' => 'U kunt dit vinden in de jaarafrekening van uw energieleverancier, opgave over een heel jaar.'
            ],
        ],

        'heating-habits' => [
            'title' => [
                'title' => 'Stookgedrag'
            ],
            'thermostat-high' => [
                'title' => 'Op welke temperatuur staat de thermostaat op de hoge stand? ',
                'help' => 'Voer hier de meest gebruikte waarde in. Als u verschillende temperaturen gebruikt kunt u ook een gemiddelde waarde invoeren. Deze informatie is nodig om een betere inschatting over de mogelijke besparing van isolatiemaatregelen te kunnen maken.'
            ],
            'thermostat-low' => [
                'title' => 'Op welke temperatuur staat de thermostaat op lage stand? ',
                'help' => 'Voer hier de meest gebruikte waarde in. Als u verschillende temperaturen gebruikt kunt u ook een gemiddelde waarde invoeren. Deze informatie is nodig om een betere inschatting over de mogelijke besparing van isolatiemaatregelen te kunnen maken.'
            ],
            'hours-high' => [
                'title' => 'Hoe veel uren per dag staat de thermostaat op hoge stand? ',
                'help' => 'Voer hier de meest gebruikte waarde in. Als u verschillende temperaturen gebruikt kunt u ook een gemiddelde waarde invoeren. Deze informatie is nodig om een betere inschatting over de mogelijke besparing van isolatiemaatregelen te kunnen maken.'
            ],
            'heating-first-floor' => [
                'title' => 'Welke situatie is van toepassing op de eerste verdieping? ',
                'help' => 'Hier kunt u aangeven of de ruimtes op de eerste verdieping wel of niet verwarmd zijn. Als u maar één bouwlaag heeft geef dan hier de situatie voor die bouwlaag op.'
            ],
            'heating-second-floor' => [
                'title' => 'Welke situatie is van toepassing op de tweede verdieping? ',
                'help' => 'Hier kunt u aangeven of de ruimtes op de tweede verdieping wel of niet verwarmd zijn. Als u geen tweede verdieping heeft kies dan “Niet van toepassing”.'
            ],
        ],

        'comment' => [
            'title' => 'Toelichting gebruik van de woning',
            'help' => 'Geef toelichting gebruik van de woning'
        ],
    ]
];