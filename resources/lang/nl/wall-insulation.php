<?php

return [
    'cavity-wall-insulation' => 'Spouwmuurisolatie',
    'facade-wall-insulation' => 'Binnengevelisolatie',
    'wall-insulation-research' => 'Er is nader onderzoek nodig hoe de gevel het beste geïsoleerd kan worden',

    'index' => [
        'costs' => [
            'gas' => [
                'title' => 'Gasbesparing',
                'help' => '<p>De besparing wordt berekend op basis van de door u ingevoerde woningkenmerken:</p><p><strong>- vierkante meters te isoleren geveloppervlakte</strong><br><strong>- type gevelisolatie</strong><br><strong>- gemiddelde stooktemperatuur in de woning (zoals bij gebruikersgedrag ingevoerd)*</strong><br><strong>- uw daadwerkelijk energiegebruik**.</strong></p><p>&nbsp;</p><p><span style="font-size: 10pt;">*De berekeningen zijn gekoppeld aan de binnentemperatuur. Bij een realistische invoer van de huidige verwarmingssituatie zal de besparing afgestemd zijn op het daadwerkelijke verbruik.</span></p><p><span style="font-size: 10pt;">**Per maatregel is er per woningtype een maximaal mogelijke besparingspercentage opgegeven. Bij gevelisolatie is bijvoorbeeld voor een tussenwoning maximaal 20 % besparing op het daadwerkelijke gasverbruik voor verwarming mogelijk. Hierdoor wordt voorkomen dat de optelsom van alle besparingen boven uw huidige gasverbruik uitkomt.</span></p><p>&nbsp;</p>',
            ],
            'co2' => [
                'title' => 'CO2 Besparing',
                'help' => '<p>Gerekend wordt met 1,88 kg/m3 gas (bron: Milieucentraal)</p>',
            ]
        ],
        'interested-in-improvement' => [
            'title' => 'Uw interesse in deze maatregel',
            'help' => 'Hier ziet u wat u bij “Algemene gegevens” over uw interesse voor gevelisolatie hebt aangegeven. Mocht u dit willen veranderen, dan kunt u dat in dit veld doen. Let wel: Aanpassingen die u hier doet zullen ook op de pagina “Algemene gegevens” mee veranderen.'
        ],
        'savings-in-euro' => [
            'title' => 'Besparing in €',
            'help' => 'Indicatieve besparing in € per jaar. De gebruikte energietarieven voor gas en elektra worden jaarlijks aan de marktomstandigheden aangepast.'
        ],
        'comparable-rent' => [
            'title' => 'Vergelijkbare rente',
            'help' => '<p>Meer informatie over de vergelijkbare rente kunt u vinden bij Milieucentraal: <a title="Link Milieucentraal" href="https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/financiering-energie-besparen/rendement-energiebesparing/" target="_blank" rel="noopener">https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/financiering-energie-besparen/rendement-energiebesparing/</a></p>'
        ],
        'indicative-costs' => [
            'title' => 'Indicatieve kosten',
            'help' => 'Hier kunt u zien wat de indicatieve kosten voor deze maatregel zijn.'
        ],
        'specific-situation' => [
            'title' => 'Toelichting op de specifieke situatie',
            'help' => 'Hier kunt u opmerkingen over uw specifieke situatie vastleggen, bijvoorbeeld voor een gesprek met een energiecoach of een uitvoerend bedrijf.'
        ],
    ],

    'taking-into-account' => [
        'repair-joint' => [
            'label' => 'Reperatie voegwerk',
            'year' => [
                'title' => 'Jaar voegwerk'
            ]
        ],
        'clean-brickwork' => [
            'label' => 'Reinigen metselwerk',
            'year' => [
                'title' => 'Jaar gevelreiniging'
            ],
        ],
        'impregnate-wall' => [
            'label' => 'Impregneren gevel',
            'year' => [
                'title' => 'Jaar gevel impregneren'
            ],
        ],
        'wall-painting' => [
            'label' => 'Gevelschilderwerk op stuk of metselwerk',
            'year' => [
                'title' => 'Jaar gevelschilderwerk'
            ]
        ]
    ]
];
