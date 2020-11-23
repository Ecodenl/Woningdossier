<?php

return [
    'floor-insulation' => [
        'help' => '<p>Hier ziet u wat u bij &ldquo;Algemene gegevens&rdquo; over de aanwezigheid van vloerisolatie heeft aangegeven. Mocht u dit willen veranderen dan kunt u dat in dit veld doen.</p>
<p>Let wel: Aanpassingen die u hier doet zullen ook op de pagina &ldquo;Algemene gegevens&rdquo; mee veranderen.</p>
<p>Als u aangeeft dat er wel vloerisolatie aanwezig is wordt er geen besparing uitgerekend! Hoeveel u kunt besparen hangt namelijk heel erg ervan af hoe dik de huidige isolatielaag is en van welke kwaliteit deze is.</p>
<p>Voor het uitrekenen van de daadwerkelijke besparing bij het na- isoleren van een reeds ge&iuml;soleerde vloer is aanvullend en gespecialiseerd advies nodig. Neem hiervoor contact op met uw energieco&ouml;peratie.</p>',
        'title' => 'De huidige situatie voor vloerisolatie',
    ],
    'title' => [
        'help' => '',
        'title' => 'Vloerisolatie',
    ],
    'intro' => [
        'title' => [
            'help' => '',
            'title' => '',
        ],
    ],
    'has-crawlspace' => [
        'help' => '<p>In het Hoomdossier wordt er alleen een berekening gemaakt als de woning een kruipruimte heeft. In andere situaties is speciaal advies nodig. U kunt dan het beste contact opnemen met uw energieco&ouml;peratie.</p>
<p>Als u kiest voor &ldquo;Onbekend&rdquo; wordt gerekend als of er een kruipruimte aanwezig is.</p>',
        'title' => 'Heeft deze woning een kruipruimte',
        'no-crawlspace' => [
            'title' => 'De vloer kan alleen van boven af geïsoleerd worden. Let op de hoogtes bij deuren en bij de trap. Vraag om aanvullend advies.',
        ],
    ],
    'crawlspace-access' => [
        'help' => '<p>Er kan alleen vloerisolatie aangebracht worden indien de kruipruimte toegankelijk is.</p>
<p>Als u kiest voor &ldquo;Onbekend&rdquo; wordt gerekend alsof de kruipruimte toegankelijk is.</p>',
        'title' => 'Is de kruipruimte toegankelijk?',
        'no-access' => [
            'title' => 'Er is aanvullend onderzoek nodig. Om de vloer te kunnen isoleren moet eerst een kruipluik gemaakt worden.',
        ],
    ],
    'crawlspace-height' => [
        'help' => '<p>Op basis van deze informatie wordt de soort isolatie bepaald:</p>
<p>- Voor het aanbrengen van <strong>vloerisolatie</strong> aan de onderzijde van de vloer moet de kruipruimte minimaal 45 tot 50 cm hoog zijn (onder de balken). <br />- Tussen 30 en 45 cm hoogte wordt gerekend met <strong>bodemisolatie</strong>.<br />- Als de kruipruimte heel laag is of als u de hoogte niet weet, wordt gerekend met de waardes voor <strong>bodemisolatie</strong>.</p>',
        'title' => 'Hoe hoog is de kruipruimte?',
    ],
    'surface' => [
        'help' => '<p>Deze waarde wordt automatisch ingevuld vanuit de gekozen voorbeeldwoning. Het gaat hierbij om de totale oppervlakte van alle vloeren die aan de grond, een kruipruimte of de buitenlucht grenzen. U kunt deze waarde aanpassen.</p>',
        'title' => 'Vloeroppervlak van de woning',
    ],
    'insulation-surface' => [
        'help' => '<p>Hier kunt u aangeven hoeveel m2 vloer u wilt isoleren. Standaard is deze waarde gelijk aan het totale vloeroppervlak.</p>',
        'title' => 'Te isoleren oppervlakte',
    ],
    'insulation-advice' => [
        'text' => [
            'help' => '<p>In dit veld wordt er een automatisch advies gegeven hoe de vloer ge&iuml;soleerd zou kunnen worden. De uitkomst is afhankelijk van de hoogte van de kruipruimte.</p>',
            'title' => 'Het volgende wordt geadviseerd:',
        ],
    ],
    'indication-for-costs' => [
        'help' => '',
        'title' => 'Indicatie voor kosten en baten voor deze maatregel',
    ],
    'crawlspace' => [
        'unknown-error' => [
            'title' => 'Onbekend! Er is aanvullend onderzoek nodig. Om de vloer te kunnen isoleren moet eerst een kruipluik gemaakt worden.',
        ],
    ],
    'comment' => [
        'title' => 'Toelichting op Vloerisolatie',
    ],
    'index' => [
        'costs' => [
            'gas' => [
                'title' => 'Gasbesparing',
                'help' => '<p>De besparing wordt berekend op basis van de door u ingevoerde woningkenmerken:</p><p><strong>- vierkante meters te isoleren vloeroppervlakte</strong><br><strong>- type vloerisolatie</strong><br><strong>- voor vloerisolatie wordt ervan uitgegaan dat de aangrenzende ruimtes verwarmd zijn</strong><br><strong>- uw daadwerkelijk energiegebruik*.</strong></p><p>&nbsp;</p><p><span style="box-sizing: border-box; font-size: 10pt;">*Per maatregel is er per woningtype een maximaal mogelijke besparingspercentage opgegeven. Bij vloerisolatie is bijvoorbeeld voor een tussenwoning maximaal 15 % besparing op het daadwerkelijke gasverbruik voor verwarming mogelijk. Hierdoor wordt voorkomen dat de optelsom van alle besparingen boven uw huidige gasverbruik uitkomt.</span></p>',
            ],
            'co2' => [
                'title' => 'CO2 Besparing',
                'help' => '<p>Gerekend wordt met 1,88 kg/m3 gas (bron: Milieucentraal)</p>',
            ],
        ],
        'interested-in-improvement' => [
            'title' => 'Uw interesse in deze maatregel',
            'help' => 'Hier ziet u wat u bij “Algemene gegevens” over uw interesse voor Vloerisolatie hebt aangegeven. Mocht u dit willen veranderen, dan kunt u dat in dit veld doen. Let wel: Aanpassingen die u hier doet zullen ook op de pagina “Algemene gegevens” mee veranderen.',
        ],
        'savings-in-euro' => [
            'title' => 'Besparing in €',
            'help' => 'Indicatieve besparing in € per jaar. De gebruikte energietarieven voor gas en elektra worden jaarlijks aan de marktomstandigheden aangepast.',
        ],
        'comparable-rent' => [
            'title' => 'Vergelijkbare rente',
            'help' => '<p>Meer informatie over de vergelijkbare rente kunt u vinden bij Milieucentraal: <a title="Link Milieucentraal" href="https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/financiering-energie-besparen/rendement-energiebesparing/" target="_blank" rel="noopener">https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/financiering-energie-besparen/rendement-energiebesparing/</a></p>',
        ],
        'indicative-costs' => [
            'title' => 'Indicatieve kosten',
            'help' => 'Hier kunt u zien wat de indicatieve kosten voor deze maatregel zijn.',
        ],
        'specific-situation' => [
            'title' => 'Toelichting op specifieke situatie',
            'help' => 'Hier kunt u opmerkingen over uw specifieke situatie vastleggen, bijvoorbeeld voor een gesprek met een energiecoach of een uitvoerend bedrijf.',
        ],
    ],
];
