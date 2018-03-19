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
					'title' => 'Wat is het voor een woning ?',
					'example-building-type' => 'Kies de best passende voorbeeldwoning',
					'what-type' => 'Wat is de woningtype ?',
					'what-user-surface' => 'Wat is de gebruiksoppervlakte van de woning',
					'how-much-building-layers' => 'Hoe veel bouwlagen heeft het huis?',
					'type-roof' => 'Type dak',
					'is-monument' => 'Is het een monument',
					'what-building-year' => 'Wat is het bouwjaar ?',
					'current-energy-label' => 'Wat is het huidige energie label?'
				],

				'energy-saving-measures' => [
					'title' => 'Welke energiebesparende maatregelen zijn al genomen in de woning?',
					'facade-insulation' => 'Gevelisolatie',
					'window-in-living-space' => 'Ramen in de leefruimtes',
					'floor-insulation' => 'Vloer isolatie',
					'roof-insulation' => 'Dak isolatie',
					'hr-cv-boiler' => 'HR CV Ketel',
					'hybrid-heatpump' => 'Hybride warmtepomp',
					'monovalent-heatpump' => 'Monovalente warmtepomp',
					'sun-panel' => [
						'title' => 'Zonnepanelen',
						'yes' => 'Zo ja, wanneer zijn panelen geplaats ?'
					],
					'sun-boiler' => 'Zonneboiler',
					'house-ventilation' => [
						'title' => 'Hoe word het huis geventileerd ?',
						'if-mechanic' => 'Indien mechanisch: wanneer is installatie geplaatst',
					],
					'additional' => 'Overig',
					'interested' => 'Intresse ?',

				],

				'data-about-usage' => [
					'title' => 'Gegevens over het gebruik van de woning',
					'total-citizens' => 'Wat is het aantal bewoners ?',
					'thermostat-highest' => 'Op welke temperatuur staat de thermostaat op de hoge stand ?',
					'thermostat-lowest' => 'Op welke temperatuur staat de thermostaat op lage stand ?',
					'max-hours-thermostat-highest' => 'Hoe veel uren per dag staat de thermostaat op hoge stand ?',
					'situation-first-floor' => 'Welke situatie is van toepassing op de eerste verdieping ?',
					'situation-second-floor' => 'Welke situatie is van toepassing op de tweede verdieping ?',
					'cooked-on-gas' => 'Wordt er op gas gekookt ?',
					'comfortniveau-warm-tapwater' => 'Wat is het comfortniveau voor het gebruik van warm tapwater ?',
					'electricity-consumption-past-year' => 'Wat is het elektragebruik van het afgelopen jaar ?',
					'gas-usage-past-year' => 'Wat is het gasgebruik van afgelopen jaar ?',
					'additional-info' => 'Toelichting op de woonsituatie'
				],
			],
		],
	],
];