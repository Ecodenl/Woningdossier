<?php

return [
    'index' => [
        'water-gas' => [
            'title' => [
                'title' => 'Warm tapwater en koken',
            ],
            'cook-gas' => [
                'title' => 'Wordt er op gas gekookt?',
            ],
            'water-comfort' => [
                'title' => 'Wat is het comfortniveau voor het gebruik van warm tapwater?',
                'help' => '<p>Het gaat om het gebruik van warm water voor keuken, douche en bad.</p>
<p><strong>Standaard</strong> = kort douchen met waterbesparende douchekop;</p>
<p><strong>Comfort</strong> = 5 - 10 minuten douchen en/of geen waterbesparende douchekop; af en toe badgebruik;</p>
<p><strong>Comfort plus</strong> = meer dan 10 minuten douchen en of regendouche / speciale luxe douche; Regelmatig badgebruik is altijd categorie comfort plus</p>',
            ],
            'resident-count' => [
                'title' => 'Wat is het aantal bewoners?',
                'help' => '<p>Hoeveel personen wonen er in de woning. Logees die alleen gedurende een aantal weken of maanden in huis wonen tellen niet mee.</p>',
            ],
        ],
        'energy-usage' => [
            'title' => [
                'title' => 'Energiegebruik',
            ],
            'gas-usage' => [
                'title' => 'Wat is het gasgebruik van afgelopen jaar?',
                'help' => '<p>U kunt dit vinden in de jaarafrekening van uw energieleverancier, opgave over een heel jaar. Neem bij voorkeur het gemiddelde van afgelopen drie jaar.</p>',
            ],
            'amount-electricity' => [
                'title' => 'Wat is het elektragebruik van het afgelopen jaar?',
                'help' => '<p>U kunt dit vinden in de jaarafrekening van uw energieleverancier, opgave over een heel jaar. Neem bij voorkeur het gemiddelde van afgelopen drie jaar.</p>
<p>Indien u zonnepanelen heeft dan wordt er meestal ook energie teruggeleverd. Op de jaarafrekening kunt u zien wat u heeft afgenomen en wat u heeft teruggeleverd. Het daadwerkelijk verbruik is de optelsom van wat direct is verbruikt uit opwek eigen panelen en wat u heeft afgenomen van het energiebedrijf.&nbsp;</p>
<p>Om het eigen verbruik uit de zonnepanelen te berekenen moet u de teruggeleverde energie aftrekken van de totale hoeveelheid opgewekte energie. Deze is meestal te vinden in de monitoringsapp van de omvormer.</p>',
            ],
        ],
        'heating-habits' => [
            'title' => [
                'title' => 'Stookgedrag',
            ],
            'thermostat-high' => [
                'title' => 'Op welke temperatuur staat de thermostaat op de hoge stand?',
                'help' => '<p>Voer hier de meest gebruikte waarde in. Als u verschillende temperaturen gebruikt kunt u ook een gemiddelde waarde invoeren. Deze informatie is nodig om een betere inschatting over de mogelijke besparing van isolatiemaatregelen te kunnen maken.</p>',
            ],
            'thermostat-low' => [
                'title' => 'Op welke temperatuur staat de thermostaat op lage stand?',
                'help' => '<p>Voer hier de meest gebruikte waarde in. Als u verschillende temperaturen gebruikt kunt u ook een gemiddelde waarde invoeren. Deze informatie is nodig om een betere inschatting over de mogelijke besparing van isolatiemaatregelen te kunnen maken.</p>',
            ],
            'hours-high' => [
                'title' => 'Hoeveel uren per dag staat de thermostaat op hoge stand?',
                'help' => '<p>Voer hier de meest gebruikte waarde in. Als u verschillende temperaturen gebruikt kunt u ook een gemiddelde waarde invoeren. Deze informatie is nodig om een betere inschatting over de mogelijke besparing van isolatiemaatregelen te kunnen maken.</p>',
            ],
            'heating-first-floor' => [
                'title' => 'Welke situatie is van toepassing op de eerste verdieping?',
                'help' => '<p>Hier kunt u aangeven of de ruimtes op de eerste verdieping wel of niet verwarmd zijn. Als u maar &eacute;&eacute;n bouwlaag heeft geef dan hier de situatie voor die bouwlaag op.</p>',
            ],
            'heating-second-floor' => [
                'title' => 'Welke situatie is van toepassing op de tweede verdieping?',
                'help' => '<p>Hier kunt u aangeven of de ruimtes op de tweede verdieping wel of niet verwarmd zijn. Als u geen tweede verdieping heeft kies dan &ldquo;Niet van toepassing&rdquo;.</p>',
            ],
        ],
        'comment' => [
            'title' => 'Toelichting gebruik van de woning',
            'help' => '<p>Hier kunnen aanvullende opmerkingen worden gemaakt over het gebruik van de woning.</p>',
        ],
    ],
];
