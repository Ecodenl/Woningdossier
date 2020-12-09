<?php

return [
    'title' => [
        'help' => '',
        'title' => 'Zonnepanelen',
    ],
    'electra-usage' => [
        'help' => 'Hier wordt nog een keer herhaald wat u bij “Algemene gegevens” over uw elektraverbruik hebt aangegeven. Mocht u dit willen veranderen dan kunt u dat in dit veld doen. Let wel: Aanpassingen die u hier doet zullen ook op de pagina “Algemene gegevens” mee veranderen.',
        'title' => 'Wat is het elektragebruik van het afgelopen jaar? (in kWh per jaar)',
    ],
    'peak-power' => [
        'help' => 'Wattpiek(Wp) is een meeteenheid dat gehanteerd wordt om het vermogen van zonnepanelen aan te geven. Hierbij is gemeten onder internationaal vastgestelde standaarden: • Sterkte van het licht  1000W/m2 • Richting van het invallende licht • Zonnespectrum (luchtmassa) • Temperatuur: 25°C Deze Wattpiek-waarde wordt gehanteerd om zonnepanelen met elkaar te vergelijken.',
        'title' => 'Piekvermogen per paneel',
    ],
    'number' => [
        'help' => 'Geef hier aan hoe veel zonnepanelen u nieuw wilt laten plaatsen, bestaande panelen hier niet meetellen. Hier boven wordt aangegeven hoe veel panelen u nodig zou hebben om uw huidig elektraverbruik met zonne-energie op te wekken.',
        'title' => 'Hoeveel zonnepanelen moeten er komen?',
    ],
    'pv-panel-orientation-id' => [
        'help' => 'Geef hier aan in welke oriëntatie de nieuwe panelen geplaatst worden.',
        'title' => 'Wat is de oriëntatie van de panelen?',
    ],
    'angle' => [
        'help' => 'Geef hier aan onder welke hellingshoek de panelen geplaatst worden. Op een hellend dak is de hellingshoek van de panelen meestal gelijk aan de dakhelling. Als de panelen in oost-west oriëntatie op een plat dak geplaatst worden kies dan bij voorkeur voor 10 graden helling. Panelen op een plat dak in zuidoriëntatie hebben bij voorkeur een hellingshoek van 20 -30 graden.',
        'title' => 'Wat is de hellingshoek van de panelen?',
    ],
    'indication-for-costs' => [
        'title' => [
            'help' => 'Hier kunt u zien wat de indicatieve kosten voor deze maatregel zijn.',
            'title' => 'Indicatieve kosten',
        ],
        'yield-electricity' => [
            'help' => 'Hier kunt u zien hoeveel kWh de nieuwe panelen op kunnen wekken. Bij de berekening van de opbrengsten worden de volgende variabelen gebruikt: - Zoninstraling per postcode 2 gebied Specifieke omstandigheden zoals schaduw of speciale types panelen worden niet meegenomen.',
            'title' => 'Opbrengst elektra',
        ],
        'raise-own-consumption' => [
            'help' => 'Hier kunt u zien hoeveel % van uw huidig elektriciteitsverbruik u met dit aantal panelen op kunt wekken.',
            'title' => 'Opwekking t.o.v. eigen verbruik',
        ],
        'comparable-rate' => [
            'help' => 'Meer informatie over de vergelijkbare rente kunt u vinden bij Milieucentraal: https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/financiering-energie-besparen/rendement-energiebesparing/',
            'title' => 'Vergelijkbare rente',
        ],
        'performance' => [
            'ideal' => 'Ideaal',
            'no-go' => 'Onrendabel',
            'possible' => 'Mogelijk',
        ],
    ],
    'advice-text' => 'Voor het opwekken van uw huidige elektraverbruik heeft u in totaal ca. :number zonnepanelen in optimale oriëntatie nodig.',
    'total-power' => 'Totale Wp vermogen van de installatie: :wp',
    'comment' => [
        'title' => 'Toelichting op Zonnepanelen',
    ],
    'index' => [
        'costs' => [
            'co2' => [
                'title' => 'CO2 Besparing',
                'help' => '<p>Gerekend wordt met 1,88 kg/m3 gas (bron: Milieucentraal)</p>',
            ],
        ],
        'interested-in-improvement' => [
            'title' => 'Uw interesse in deze maatregel',
            'help' => 'Hier ziet u wat u bij “Algemene gegevens” over uw interesse voor zonnepanelen hebt aangegeven. Mocht u dit willen veranderen, dan kunt u dat in dit veld doen. Let wel: Aanpassingen die u hier doet zullen ook op de pagina “Algemene gegevens” mee veranderen.',
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
