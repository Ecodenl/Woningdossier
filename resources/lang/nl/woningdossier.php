<?php

return [
	'navbar' => [
		'language' => 'Taal',
		'languages' => [
			'nl' => 'Nederlands',
			'en' => 'Engels',
		],
	],
	'cooperation' => [
        'radiobutton' => [
            'not-important' => 'Niet van toepassing',
            'yes' => 'Ja',
            'no' => 'Nee',
            'unknown' => 'Onbekend',
            'mostly' => 'Gedeeltelijk',
        ],
		'my-account' => [
			'settings' => [
				'form' => [
					'index' => [
						'header' => 'Mijn account',
						'submit' => 'Update',
					],
					'store' => [
						'success' => 'Gegevens succesvol gewijzigd',
					],
					'destroy' => [
						'header' => 'Account verwijderen',
						'label' => 'Mijn account verwijderen',
						'submit' => 'Verwijderen',
					]
				],
			],
			'cooperations' => [
				'form' => [
					'header' => 'Mijn coöperaties',
				],

			],
		],

		'tool' => [
			'title' => 'Keukentafel tool',

			'example-buildings' => [
				'woning0' => 'Er is geen passende voorbeeldwoning',
				'woning1' => 'Tussenwoning, drie bouwlagen en plat dak',
				'woning2' => 'Hoekwoning, drie bouwlagen en plat dak',
				'woning3' => 'Benedenwoning zonder opkamer (tussenwoning)',
				'woning4' => 'Benedenwoning zonder opkamer (hoekwoning)',
				'woning5' => 'Hoekhuis, twee bouwlagen en nieuwe dakopbouw',
				'woning6' => 'Tussenwoning, twee bouwlagen en nieuwe dakopbouw',
				'woning7' => 'Tussenwoning, twee bouwlagen en plat dak',
				'woning8' => 'Arbeidershuis, twee bouwlagen (tussenwoning)',
				'woning9' => 'Jaren \'30 tussenwoning met hellend dak',
				'woning10' => 'Jaren \'30 hoekwoning met hellend dak',
				'woning11' => 'Tussenwoning, drie bouwlagen en hellend dak',
				'woning12' => 'Hoekwoning, drie bouwlagen en hellend dak',
				'woning13' => 'Bovenwoning zonder opkamer (tussenwoning)',
				'woning14' => 'Bovenwoning zonder opkamer (hoekwoning)',
			],
			'roof-types' => [
				'type0' => 'Hellend dak met dakpannen',
				'type1' => 'Hellend dak met bitumen',
				'type2' => 'Platdak',
				'type3' => 'Geen dak',
			],
			'interests' => [
				'interest0' => 'Ja, op korte termijn',
				'interest1' => 'Ja, op termijn',
				'interest2' => 'Meer informatie gewenst',
				'interest3' => 'Geen actie',
				'interest4' => 'Niet mogelijk',
			],

			'general-data' => [
				'title' => 'Algemene gegevens',

				'name-address-data' => [
					'title' => 'Naam en adresgegevens',
					'name-resident' => 'Naam bewoner',
					'street' => 'Straat',
					'house-number' => 'Huisnummer',
					'zip-code' => 'Postcode',
					'residence' => 'Plaats',
					'email' => 'e-mail adres',
					'phone-number' => 'Telefoon'
				],

				'building-type' => [
					'title' => 'Wat is het voor woning?',
					'example-building-type' => 'Kies de best passende voorbeeldwoning',
					'what-type' => 'Wat is de woningtype?',
					'what-user-surface' => 'Wat is de gebruiksoppervlakte van de woning?',
					'how-much-building-layers' => 'Hoe veel bouwlagen heeft het huis?',
					'type-roof' => 'Type dak',
					'is-monument' => 'Is het een monument?',
					'what-building-year' => 'Wat is het bouwjaar?',
					'current-energy-label' => 'Wat is het huidige energielabel?'
				],

				'energy-saving-measures' => [
					'title' => 'Welke energiebesparende maatregelen zijn al genomen in de woning?',
					'facade-insulation' => 'Gevelisolatie',
					'window-in-living-space' => 'Ramen in de leefruimtes',
					'window-in-sleeping-spaces' => 'Ramen in de slaapruimtes',
					'floor-insulation' => 'Vloerisolatie',
					'roof-insulation' => 'Dakisolatie',
					'hr-cv-boiler' => 'HR CV Ketel',
					'hybrid-heatpump' => 'Hybride warmtepomp',
					'monovalent-heatpump' => 'Monovalente warmtepomp',
					'sun-panel' => [
						'title' => 'Aantal zonnepanelen',
                        'if-mechanic' => 'Indien mechanisch: wanneer is installatie geplaatst?',
					],
					'sun-boiler' => 'Zonneboiler',
					'house-ventilation' => [
						'title' => 'Hoe wordt het huis geventileerd?',
						'if-mechanic' => 'Indien mechanisch: wanneer is installatie geplaatst?',
					],
					'additional' => 'Overig',
					'interested' => 'Interesse?',

				],
				'data-about-usage' => [
					'title' => 'Gegevens over het gebruik van de woning',
					'total-citizens' => 'Wat is het aantal bewoners?',
					'thermostat-highest' => 'Op welke temperatuur staat de thermostaat op de hoge stand?',
					'thermostat-lowest' => 'Op welke temperatuur staat de thermostaat op lage stand?',
					'max-hours-thermostat-highest' => 'Hoe veel uren per dag staat de thermostaat op hoge stand?',
					'situation-first-floor' => 'Welke situatie is van toepassing op de eerste verdieping?',
					'situation-second-floor' => 'Welke situatie is van toepassing op de tweede verdieping?',
					'cooked-on-gas' => 'Wordt er op gas gekookt?',
					'comfortniveau-warm-tapwater' => 'Wat is het comfortniveau voor het gebruik van warm tapwater?',
					'electricity-consumption-past-year' => 'Wat is het elektragebruik van het afgelopen jaar? (in kWh per jaar)',
					'gas-usage-past-year' => 'Wat is het gasgebruik van afgelopen jaar? (in m3 gas per jaar)',
					'additional-info' => 'Toelichting op de woonsituatie'
				],
			],
            'wall-insulation' => [
                'intro' => [
                    'title' => 'Gevelisolatie',
                    'build-year' => 'Het huis is gebouwd in :year. Woningen met dit bouwjaar hebben vaak geen spouwmuur.',
                    'filled-insulation' => 'U hebt de volgende isolatie ingevuld voor de gevel, weet u nu meer? Pas de waarde dan hier aan.',
                    'has-cavity-wall' => 'Heeft deze woning een spouwmuur ?',
                    'is-facade-plastered-painted' => 'Is de gevel gestuct of geverfd ?',
                    'surface-paintwork' => 'Wat is de oppervlakte van de geschilderde gevel ?',
                    'damage-paintwork' => 'Is er schade aan het gevelschilderwerk ?'
                ],

                'optional' => [
                    'title' => 'Optioneel: Vragen over de staat van onderhoud van de gevel',
                    'flushing' => 'Zijn er voegen die loslaten of uitgebroken zijn ?',
                    'if-facade-dirty' => 'Is de gevel vervuild (aanslag op de stenen) ? ',
                    'house-with-same-situation' => 'Woningen met dezelfde situatie hebben vaak deze geveloppervlakte.',
                    'not-right' => 'Klopt dit niet? Vul dan hier het juiste getal in, als je het niet weet laat dit veld vrij.',
                    'facade-best-insulation' => 'De gevel kan het beste op de volgende manier geïsoleerd worden',
                ],

                'indication-for-costs' => [
                    'title' => 'Indicatie voor kosten en baten voor deze maatregel',
                    'gas-savings' => 'Gasbesparing',
                    'co2-savings' => 'CO2 Besparing',
                    'savings-in-euro' => 'Besparing in €',
                    'indicative-costs' => 'Indicatieve kosten',
                    'comparable-rate' => 'Vergelijkbare rente',
                    'year' => 'Jaar',
                ],

                'taking-into-account' => [
                    'title' => 'U kunt de komende jaren met de volgende onderhoudsmaatregelen rekening houden:',
                    'sub-title' => 'Het is aan te raden om stukken gevel die nu al heel slecht zijn meteen aan te pakken.',
                    'expected-costs' => 'Te verwachten kosten voor deze maatregel',
                    'explanation-specific-situation' => 'Toelichting over de specifieke situatie',
                    'repair-joint' => 'Reparatie voegwerk',
                    'clean-brickwork' => 'Reinigen metselwerk',
                    'impregnate-wall' => 'Impregneren gevel',
                    'wall-painting' => 'Gevelschilderwerk op stuk of metselwerk',
                    'year' => 'Jaar',
                    'additional-info' => 'Toelichting over de specifieke situatie',
                ],
            ],

            'insulated-glazing' => [
                'title' => 'Isolerende beglazing',

                'information' => [
                    'interested-to-measure' => [
                        'title' => 'Bent u geintreseerd in één of meerdere van deze maatregelen?',
                    ],
                    'current-glass' => [
                        'title' => 'Wat voor glas is er nu?'
                    ],
                    'are-rooms-heater' => [
                        'title' => 'Zijn de kamers verwarmd'
                    ],
                    'replace-glass-in-lead' => 'Glas in lood vervangen',
                    'place-hr-only-glass' => 'Plaatsen van HR++ glas (alleen het glas)',
                    'place-hr-with-frame' => 'Plaatsen van HR++ glas (Inclusief kozijn)',
                    'triple-hr-glass' => 'Drievoudige HR beglazing inclusief kozijn',
                ],


            ]
		],
	],
];