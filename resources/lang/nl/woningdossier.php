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
        'home' => [
            'disclaimer' => [
                'title' => 'Disclaimer voor het gebruik van de tool.',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta, ea exercitationem facilis hic magni mollitia neque, non quo ratione sed sequi similique suscipit ullam unde voluptatibus. Impedit optio quasi tempora?',

            ],
        ],
        'help' => [
            'title' => 'Help',
            'help' => [
                'title' => 'Hulp met het gebruik van de tool.',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta, ea exercitationem facilis hic magni mollitia neque, non quo ratione sed sequi similique suscipit ullam unde voluptatibus. Impedit optio quasi tempora?',
            ],
        ],
        'measure' => [
            'title' => 'Maatregelen',
            'measure' => [
                'title' => 'Maatregelen',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta, ea exercitationem facilis hic magni mollitia neque, non quo ratione sed sequi similique suscipit ullam unde voluptatibus. Impedit optio quasi tempora?',
            ],
        ],
		'disclaimer' => [
			'title' => 'Disclaimer',
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

			'unit' => [
				'year' => 'jaar',
				'liter' => 'liter',
				'day' => 'dag',
			],

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
					'gas-usage-past-year' => 'Wat is het gasgebruik van afgelopen jaar? (in m<sup>3</sup> gas per jaar)',
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

                'alert' => [
                    'description' => 'Let op, geverfde of gestukte gevels kunnen helaas niet voorzien worden van spouwmuurisolatie'
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
                    'co2-savings' => 'CO<sub>2</sub> Besparing',
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
                'm2' => 'm<sup>2</sup>',
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
                'comments' => 'Opmerkingen.',
            ],

			'floor-insulation' => [
			    'intro' => [
			        'title' => 'Vloerisolatie',
                ],
				'title' => 'Vloerisolatie',
				'floor-insulation' => 'U hebt de volgende isolatie ingevuld voor de vloer weet u nu meer? Pas de waarde dan hier aan',
				'has-crawlspace' => [
					'title' => 'Heeft deze woning een kruipruimte',
					'unknown' => 'Er is nader onderzoek nodig of de vloer geïsoleerd kan worden',
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
					'is-pitched-roof-insulated' => 'is het hellende dak geïsoleerd?',
					'bitumen-insulated' => 'Wanneer is het bitumen dak voor het laatst vernieuwd?',
					'flat-roof-surface' => 'Dakoppervlak van platte dak',
					'pitched-roof' => 'Is het hellende dak geïsoleerd?',
					'in-which-condition-tiles' => 'In welke staat verkeren de dakpannen?',
					'zinc-replaced' => 'Wanneer is het zinkwerk voor het laatst vernieuwd?',
					'pitched-roof-surface' => 'Dakoppervlak hellend dak',
				],

				'flat-roof' => [
					'title' => 'Plat dak',
					'insulate-roof' => 'Hoe wilt u het platte dak isoleren?',
					'situation' => 'Welke situatie is van toepassing voor de ruimtes direct onder het platte dak?',
				],
				'pitched-roof' => [
					'title' => 'Hellend dak',
					'insulate-roof' => 'Hoe wilt u het hellende dak isoleren?',
					'situation' => 'Welke situatie is van toepassing voor de ruimtes direct onder het hellende dak?',
				],
				'measure-application' => [
					'no' => 'Nee',
				],

				'costs' => [
					'gas' => 'Gasbesparing',
					'co2' => 'CO<sub>2</sub> Besparing',
					'savings-in-euro' => 'Besparing in €',
					'indicative-costs-insulation' => 'Indicatieve kosten aanbrengen isolatie',
					'comparable-rent' => 'Vergelijkbare rente',
					'flat' => [
						'title' => 'Kosten en baten voor isoleren van het platte dak',
						'indicative-costs-replacement' => 'Indicatieve kosten vervanging dakbedekking',
						'indicative-replacement-year' => 'Indicatie vervangingsmoment dakbedekking',
					],
					'pitched' => [
						'title' => 'Kosten en baten voor isoleren van het hellende dak',
						'indicative-costs-replacement' => 'Indicatieve kosten vervanging dakpannen',
						'indicative-replacement-year' => 'Indicatie vervangingsmoment dakpannen',
					],
				],
			],
			'boiler' => [
				'title' => 'HR CV Ketel',

				'current-gas-usage' => 'Huidig gasverbruik',
				'resident-count' => 'Huidig aantal bewoners',
				'boiler-type' => 'Wat is het type van de huidige CV ketel',
				'boiler-placed-date' => 'Wanneer is de huidige CV ketel geplaatst?',
				'already-efficient' => 'Je hebt al een efficiënte CV ketel. Het vervangen zal alleen een beperkte energiebesparing opleveren',

				'indication-for-costs' => [
					'title' => 'Indicatie voor kosten en baten voor deze maatregel',
					'gas-savings' => 'Gasbesparing',
					'co2-savings' => 'CO<sub>2</sub> Besparing',
					'savings-in-euro' => 'Besparing in €',
					'indicative-costs' => 'Indicatieve kosten',
					'indicative-replacement' => 'Indicatie vervangingsmoment cv ketel',
					'comparable-rate' => 'Vergelijkbare rente',
					'year' => 'Jaar',
				],
			],
			'solar-panels' => [
				'title' => 'Zonnepanelen',

				'peak-power' => 'Piekvermogen per paneel',
				'advice-text' => 'Voor het opwekken van uw huidige elektraverbruik heeft u ca. :number zonnepanelen in optimale oriëntatie nodig',
				'number' => 'Hoeveel zonnepanelen moeten er komen?',
				'pv-panel-orientation-id' => 'Wat is de oriëntatie van de panelen?',
				'angle' => 'Wat is de hellingshoek van de panelen?',
				'total-power' => 'Totale Wp vermogen van de installatie: :wp',
				'indication-for-costs' => [
					'title' => 'Indicatie voor kosten en baten voor deze maatregel',
					'yield-electricity' => 'Opbrengst elektra',
					'raise-own-consumption' => 'Opwekking t.o.v. eigen verbruik',
					'co2-savings' => 'CO<sub>2</sub> Besparing',
					'savings-in-euro' => 'Besparing in €',
					'indicative-costs' => 'Indicatieve kosten',
					'comparable-rate' => 'Vergelijkbare rente',
					'performance-of-system' => 'Prestatie van het systeem: :performance',
					'year' => 'Jaar',
					'performance' => [
						'ideal' => 'Ideaal',
						'possible' => 'Mogelijk',
						'no-go' => 'Onrendabel',
					],
				],
			],
			'heater' => [
				'title' => 'Zonneboiler',

				'comfort-level-warm-tap-water' => 'Comfortniveau voor het gebruik van warm tapwater',
				'pv-panel-orientation-id' => 'Oriëntatie van de collector',
				'angle' => 'Hellingshoek van de collector',

				'estimated-usage' => 'Geschat huidig gebruik',
				'consumption-water' => 'Gebruik warm tapwater',
				'consumption-gas' => 'Bijhorend gasverbruik',

				'system-specs' => 'Specificaties systeem',
				'size-boiler' => 'Grootte zonneboiler',
				'size-collector' => 'Grootte collector',

				'indication-for-costs' => [
					'title' => 'Indicatie voor kosten en baten voor deze maatregel',
					'production-heat' => 'Warmteproductie per jaar',
					'percentage-consumption' => 'Aandeel van de zonneboiler aan het totaalverbruik voor warm water',

				],
			],

			'my-plan' => [
				'title' => 'Actieplan',
				'description' => 'Onderstaande adviesmaatregelen zijn gebaseerd op de resultaten van de keukentafeltool. Met deze adviezen kunt u onderstaand uw meerjarenonderhoudsplan vormgeven',

				'energy-saving-measures' => 'Energiebesparende maatregelen',
				'maintenance-measures' => 'Onderhoud',

				'maintenance-plan' => 'Uw persoonlijke meerjarenonderhoudsplan',
				'no-year' => 'Geen jaartal',

				'columns' => [
					'interest' => 'Interesse',
					'measure' => 'Maatregel',
					'costs' => 'Kosten',
					'savings-gas' => 'Besparing m<sup>3</sup> gas',
					'savings-electricity' => 'Besparing kWh elektra',
					'savings-costs' => 'Besparing in euro',
					'advice-year' => 'Geadviseerd',
					'planned-year' => 'Planning',
				],
			],

            'ventilation-information' => [
                'title' => 'Informatie pagina over ventilatie.',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta, ea exercitationem facilis hic magni mollitia neque, non quo ratione sed sequi similique suscipit ullam unde voluptatibus. Impedit optio quasi tempora?',

                'downloads' => [
                    'title' => 'Downloadbare informatie.',
                    'content' => 'Pdf informatie...'
                ],
            ],

			'heat-pump-information' => [
				'title' => 'Informatie pagina over warmtepomp.',
				'description' => '',
				'downloads' => [
					'title' => 'Downloadbare informatie.',
					'content' => 'Pdf informatie...'
				],
			],

            'heat-pump' => [
                'title' => 'Warmtepomp',
                'description' => 'Een warmtepomp zorgt op een milieuvriendelijke manier voor verwarming van uw huis en warm water in de douche en keuken. Het is een duurzaam alternatief voor uw cv-ketel op gas: uw CO2-uitstoot voor verwarming daalt met zo\'n 50 tot 60 procent! Bovendien kunt u bij aankoop subsidie krijgen en gaat uw energierekening omlaag.<br><br><strong>Wat is een warmtepomp?</strong><br> Een warmtepomp is een onderdeel van een centrale verwarmingsinstallatie en zorgt ervoor dat het verwarmingswater wordt verwarmd en naar de laagtemperatuur verwarmingselementen zoals bijvoorbeeld vloerverwarming wordt gepompt. Meestal zorgt de warmtepomp ook voor warmtapwater, voor o.a. douchen en afwassen. We spreken dan van een combiwarmtepomp. Als de warmtepomp gebruikt wordt naast een cv-ketel die de piekvraag oplost, spreken we van een hybride- warmtepomp. <br><br><strong>Welke varianten zijn er?</strong><br>Warmtepompen zijn in verschillende soorten en maten verkrijgbaar. Belangrijk is welke energiebron wordt toegepast. Dat kan de bodem of de buitenlucht zijn. Het is belangrijk om een warmtepomp te kiezen die past bij uw woning. Hoe groter uw huis, hoe meer capaciteit er nodig is. Bij een combiwarmtepomp is daarnaast de CW-waarde belangrijk. Hoe hoger deze waarde, hoe meer warmtapwater de warmtepomp kan produceren.<br><br><strong>Hoeveel kan ik besparen?</strong><br>De rekenmethodiek voor het berekenen van de kosten en baten binnen het woondossier is op dit moment nog in ontwikkeling. Binnenkort kunt u hier terecht voor een indicatie wat een warmtepomp in uw situatie aan besparing op kan leveren.<br><br>Bij vragen over warmtepompen kunt u terecht bij uw coöperatie.',
                'current-gas-usage' => 'Huidig gasverbruik',
                'heat-pump-type' => 'Kies de soort warmtepomp',
                'gas-usage-for-tapwater' => 'Gasgebruik voor warm tapwater',
                'gas-usage-for-heating' => 'Gasgebruik voor de verwarming',

                'net-gas-usage' => 'Netto gasgebruik obv rendement',
                'energy-content' => 'Energieinhoud',
                'heat' => 'Warmte',
                'cop' => 'COP',
                'electro-usage-heatpump' => 'Elektragebruik door de warmtepomp',

                'hybrid-heatpump' => [
                    'title' => 'Hybride warmtepomp met buitenlucht als warmtebron',
                    'indication-for-costs' => [
                        'title' => 'Indicatie voor kosten en baten voor deze maatregel',
                        'gas-savings' => 'Gasbesparing',
                        'co2-savings' => 'CO<sub>2</sub> Besparing',
                        'savings-in-euro' => 'Besparing in €',
                        'moreusage-electro-in-euro' => 'Meerverbruik in elektra in €',
                        'electro-usage-heatpump' => 'Elektragebruik door de warmtepomp',
                        'saldo' => 'Saldo',
                        'indicative-costs' => 'Indicatieve kosten',
                        'comparable-rate' => 'Vergelijkbare rente',
                        'year' => 'Jaar',
                    ],
                ],
                'full-heatpump' => [
                    'title' => 'Volledige heatpump',
                    'current-heating' => 'Hoe word de woning nu verwarmd?',
                    'wanted-heat-source' => 'Welke soort warmtebron is gewenst?',
                    'heat-usage' => [
                        'heater' => 'Warmtegebruik voor verwarming',
                        'warm-tapwater' => 'Warmtegebruik voor warm tapwater'
                    ],
                    'indication-for-costs' => [
                        'title' => 'Indicatie voor kosten en baten voor deze maatregel',
                        'gas-savings' => 'Gasbesparing',
                        'co2-savings' => 'CO<sub>2</sub> Besparing',
                        'savings-in-euro' => 'Besparing in €',
                        'moreusage-electro-in-euro' => 'Meerverbruik in elektra in €',
                        'electro-usage-heatpump' => 'Elektragebruik door de warmtepomp',
                        'saldo' => 'Saldo',
                        'indicative-costs' => 'Indicatieve kosten',
                        'comparable-rate' => 'Vergelijkbare rente',
                        'year' => 'Jaar',
                    ],
                ],
            ],
		],
    ],
];