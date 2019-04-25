<?php

return [
	'general'         => [
		'BANK_INTEREST_PER_YEAR'                       => [
			'title' => 'Bankrente per jaar',
			'unit'  => '%',
		],
		'INTEREST_PERIOD'                              => [
			'title' => 'Rente periode',
			'unit'  => 'jaar',
		],
		'EURO_SAVINGS_GAS'                             => [
			'title' => 'Energiekosten gas',
			'unit'  => '€/m<sup>3</sup> gas',
		],
		'EURO_SAVINGS_ELECTRICITY'                     => [
			'title' => 'Energiekosten elektra',
			'unit'  => '€/kWh',
		],
		'CO2_SAVING_GAS'                               => [
			'title' => 'CO<sub>2</sub> besparing gas',
			'unit'  => 'kg/m<sup>3</sup> gas',
		],
		'CO2_SAVINGS_ELECTRICITY'                      => [
			'title' => 'CO<sub>2</sub> besparing elektra',
			'unit'  => 'kg/kWh',
		],
		'PERCENTAGE_GAS_SAVINGS_PLACE_CRACK_SEALING'   => [
			'title' => 'Energiebesparing door aanbrengen kierdichting',
			'unit'  => '%',
		],
		'PERCENTAGE_GAS_SAVINGS_REPLACE_CRACK_SEALING' => [
			'title' => 'Energiebesparing door vervangen kierdichting',
			'unit'  => '%',
		],
		'GAS_CALORIFIC_VALUE'                          => [
			'title' => 'Kalorische waarde van het gas',
			'unit'  => 'MJ'
		],
		'GAS_CONVERSION_FACTOR'                        => [
			'title' => 'Omrekenfactor MJ in kWh',
			'unit'  => 'MJ/Wh',
		],
	],
	'max-savings' => [
		'prefix' => 'Maximale besparing',
	],
	'price-indexes' => [
		'gas' => [
			'title' => 'Prijsstijging gas',
			'unit' => '% per jaar',
		],
		'electricity' => [
			'title' => 'Prijsstijging elektra',
			'unit' => '% per jaar',
		],
		'common' => [
			'title' => 'Prijsstijging algemeen',
			'unit' => '% per jaar',
		],
	],
	'wall-insulation' => [
		'AVERAGE_TEMPERATURE_NORM' => [
			'title' => 'Gemiddelde temperatuur in de normberekening',
			'unit'  => 'graden',
		],
		'WALL_INSULATION_JOINTS_DEFAULT' => [
			'title' => 'Spouwmuurisolatie, standaard',
			'unit',
		],
		'WALL_INSULATION_JOINTS_CORRECTION' => [
			'title' => 'Spouwmuurisolatie, correctiefactor',
			'unit',
		],
		'WALL_INSULATION_FACADE_DEFAULT' => [
			'title' => 'Binnengevelisolatie, standaard',
			'unit',
		],
		'WALL_INSULATION_FACADE_CORRECTION' => [
			'title' => 'Binnengevelisolatie, correctiefactor',
			'unit',
		],
		'WALL_INSULATION_RESEARCH_DEFAULT' => [
			'title' => 'Er is nader onderzoek nodig hoe de gevel het beste geïsoleerd kan worden, standaard',
			'unit',
		],
		'WALL_INSULATION_RESEARCH_CORRECTION' => [
			'title' => 'Er is nader onderzoek nodig hoe de gevel het beste geïsoleerd kan worden, correctiefactor',
			'unit',
		],
	],
	'roof-insulation' => [
		'ROOF_INSULATION_PITCHED_INSIDE_2' => [
			'title' => 'Isolatie hellend dak van binnen uit, verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_PITCHED_INSIDE_3' => [
			'title' => 'Isolatie hellend dak van binnen uit, matig verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_PITCHED_INSIDE_4' => [
			'title' => 'Isolatie hellend dak van binnen uit, niet verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_PITCHED_REPLACE_TILES_2' => [
			'title' => 'Isolatie hellend dak met vervaning van dakpannen, verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_PITCHED_REPLACE_TILES_3' => [
			'title' => 'Isolatie hellend dak met vervaning van dakpannen, matig verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_PITCHED_REPLACE_TILES_4' => [
			'title' => 'Isolatie hellend dak met vervaning van dakpannen, niet verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_FLAT_ON_CURRENT_2' => [
			'title' => 'Isolatie plat dak op huidige dakbedekking, verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_FLAT_ON_CURRENT_3' => [
			'title' => 'Isolatie plat dak op huidige dakbedekking, matig verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_FLAT_ON_CURRENT_4' => [
			'title' => 'Isolatie plat dak op huidige dakbedekking, niet verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_FLAT_REPLACE_2' => [
			'title' => 'Isolatie plat dak met vervanging van dakbedekking, verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_FLAT_REPLACE_3' => [
			'title' => 'Isolatie plat dak met vervanging van dakbedekking, matig verwarmd',
			'unit' => '',
		],
		'ROOF_INSULATION_FLAT_REPLACE_4' => [
			'title' => 'Isolatie plat dak met vervanging van dakbedekking, niet verwarmd',
			'unit' => '',
		],
	],
	'floor-insulation' => [
		'FLOOR_INSULATION_FLOOR' => [
			'title' => 'Vloerisolatie',
			'unit' => '',
		],
		'FLOOR_INSULATION_BOTTOM' => [
			'title' => 'Bodemisolatie',
			'unit' => '',
		],
		'FLOOR_INSULATION_RESEARCH' => [
			'title' => 'Er is nader onderzoek nodig of de vloer geïsoleerd kan worden',
			'unit' => '',
		],
	],
	'boiler' => [
		'wtw' => 'Warm tapwater',
		'heating' => 'Verwarming',
	],
	'heater'          => [
		'M3_GAS_TO_KWH' => [
			'title' => 'kWh per m<sup>3</sup> gas',
			'unit'  => 'kWh',
		],
	],
	'pv-panels' => [
		'COST_WP' => [
			'title' => 'kosten wp',
			'unit' => '€/wp',
		],
		'COST_KWH' => [
			'title' => 'kosten kWh',
			'unit' => '€/kWh',
		],
	]
];