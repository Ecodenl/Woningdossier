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
        'option' => [
            'yes' => 'Ja',
            'no' => 'Nee',
            'unknown' => 'Onbekend',
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
					'what-type' => 'Wat is het woningtype?',
					'what-user-surface' => 'Wat is de gebruiksoppervlakte van de woning?',
					'how-much-building-layers' => 'Hoeveel bouwlagen heeft het huis?',
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
					'additional-info' => 'Toelichting op de woonsituatie',

                    'motivation' => [
                        'title' => 'Wat is de motivatie om aan de slag te gaan',
                        'priority' => 'Priotiteit :prio',
                    ],
                    'motivation-extra' => 'Toelichting op de motivatie',
				],
			],
            'wall-insulation' => [
                'intro' => [
                    'title' => 'Gevelisolatie',
                    'build-year' => 'Het huis is gebouwd in :year.',
	                'build-year-post-1985' => 'Bij woningen met dit bouwjaar is de gevel vaak al tijdens de bouw geïsoleerd',
	                'build-year-post-1930' => 'Woningen met dit bouwjaar hebben vaak wel een spouwmuur',
	                'build-year-pre-1930' => 'Woningen met dit bouwjaar hebben vaak geen spouwmuur',
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
	                'facade-surface' => 'Geveloppervlakte van de woning',
                ],

	            'insulation-advice' => [
	            	'text' => 'De gevel kan het beste op de volgende manier geïsoleerd worden',
		            'cavity-wall' => 'Spouwmuurisolatie',
		            'facade-internal' => 'Binnengevelisolatie',
		            'research' => 'Er is nader onderzoek nodig hoe de gevel het beste geïsoleerd kan worden',
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

                'glass-in-lead' => [
                    'title' => 'Glas in lood vervangen',

                ],

                'place-hr-with-frame' => [
                    'title' => 'Plaatsen van HR++ glas (Inclusief kozijn)',
                ],
                'triple-hr-glass' => [
                    'title' => 'Drievoudige HR beglazing inclusief kozijn',

                ],

                'place-hr-only-glass' => [
                   'title' => 'Plaatsen van HR++ glas (alleen het glas)',
                ],

                'current-glass' => 'Wat voor glas is er nu?',
                'heated-rooms' => 'Zijn de kamers verwarmd?',
                'm2' => 'm2',
                'total-windows' => 'Aantal ramen',

                'moving-parts-quality' => 'Zijn de draaiende delen van ramen en deuren voorzien van kierdichting?',

                'facade-surface' => 'Geveloppervlakte van de woning',
                'windows-surface' => 'Totale raamoppervlakte van de woning',

                'paint-work' => [
                    'title' => 'Vragen over het schilderwerk',
                    'which-frames' => 'Welke kozijnen heeft uw huis?',
                    'other-wood-elements' => 'Welke andere houten bouwdelen zijn aanwezig in uw huis?',
                    'last-paintjob' => 'Wanneer is het schilderwerk voor het laatst gedaan? (jaargetal)',
                    'paint-damage-visible' => 'Is verfschade waarneembaar? (barsten / bladders/ blazen)',
                    'wood-rot-visible' => 'Is houtrot waarneembaar?'
                ],

	            'taking-into-account' => [
	            	'paintwork' => 'Indicatieve kosten schilderwerk',
		            'paintwork_year' => 'Volgende schilderbeurt aanbevolen',
	            ],
            ],

			'floor-insulation' => [
				'title' => 'Vloerisolatie',
				'floor-insulation' => 'U hebt de volgende isolatie ingevuld voor de vloer weet u nu meer? Pas de waarde dan hier aan',
				'has-crawlspace' => [
					'title' => 'Heeft deze woning een kruipruimte',
					'no-crawlspace' => 'De vloer kan alleen van boven af geïsoleerd worden. Let op de hoogtes bij deuren en bij de trap. Vraag om aanvullend advies.',
				],
				'crawlspace-access' => [
					'title' => 'Is de kruipruimte toegankelijk?',
					'no-access' => 'Er is aanvullend onderzoek nodig. Om de vloer te kunnen isoleren moet eerst een kruipluik gemaakt worden.',
				],
				'crawlspace-height' => 'Hoe hoog is de kruipruimte?',
				'floor-surface' => 'Vloeroppervlak van de woning',
				'insulation-advice' => [
					'text' => 'De vloer kan het beste op de volgende manier geïsoleerd worden',
					'floor' => 'Vloerisolatie',
					'bottom' => 'Bodemisolatie',
					'research' => 'Er is nader onderzoek nodig of de vloer geïsoleerd kan worden',
				],
			],
			'roof-insulation' => [
				'title' => 'Dakisolatie',
				'current-situation' => [
					'title' => 'Huidige situatie',
					'roof-types' => 'Wat voor daktypes zijn aanwezig in uw woning?',
					'main-roof' => 'Wat is het hoofddak?',
					'is-flat-roof-insulated' => 'is het platte dak geïsoleerd?',
					'bitumen-insulated' => 'Wanneer is het bitumen dak voor het laatst vernieuwd?',
					'flat-roof-surface' => [
						'comparable-houses' => 'Vergelijkbare woningen hebben een plat dak van :m2 m<sup>2</sup>',
						'not-right' => 'Klopt dit oppervlak? Zo niet, wijzig het dan hier.',
					],
					'pitched-roof' => 'Is het hellende dak geïsoleerd?',
					'in-which-condition-tiles' => 'In welke staat verkeren de dakpannen?',
					'zinc-replaced' => 'Wanner is het zinkwerk voor het laats vernieuwd?',
					'pitched-roof-surface' => [
						'comparable-houses' => 'Vergelijkbare woningen hebben een hellend dak van :m2 m<sup>2</sup>',
						'not-right' => 'Klopt dit oppervlak? Zo niet, wijzig het dan hier.',
					],
				],

				'flat-roof' => [
					'title' => 'Plat dak',
					'insulate-roof' => 'Wilt u een plat dak isoleren',
					'situation' => 'Welke situatie is van toepassing voor de ruimtes direct onder het platte dak?',
				],
				'pitched-roof' => [
					'title' => 'Hellend dak',
					'insulate-roof' => 'Wilt u het hellende dak isoleren',
					'situation' => 'Welke situatie is van toepassing voor de ruimtes direct onder het hellende dak?',
				],

				'costs' => [
					'title' => 'Kosten en baten voor isoleren van het :type dak',
					'gas' => 'Gasbesparing',
					'co2' => 'CO2 Besparing',
					'savings-in-euro' => 'Besparing in €',
					'indicative-costs-insulation' => 'Indicatieve kosten annbrengen insolatie',
					'indicative-costs-replacement' => 'indicatieve kosten vervanging dakbedekking',
					'indicative-replace-date' => 'Indicatie vervangingsmoment dakbedekking',
					'comparable-rent' => 'Vergelijkbare rente'
				],
			],
		],
	],
];