<?php

return [
	'navbar' => [
		'language' => 'Language',
		'languages' => [
			'nl' => 'Dutch',
			'en' => 'English',
		],
	],
	'cooperation' => [
        'radiobutton' => [
            'yes' => 'Yes',
            'no' => 'No',
            'unknown' => 'Unknown',
            'mostly' => 'Mostly'
        ],
		'my-account' => [
			'settings' => [
				'form' => [
					'index' => [
						'header' => 'My account',
						'submit' => 'Update',
					],
					'store' => [
						'success' => 'Your data was successfully updated',
					],
					'destroy' => [
						'header' => 'Delete account',
						'label' => 'Delete my account',
						'submit' => 'Delete',
					]
				],
			],
			'cooperations' => [
				'form' => [
					'header' => 'My cooperations',
				],
			],
		],
		'tool' => [
			'title' => 'Kitchen table tool',

            'wall-insulation' => [
                'intro' => [
                    'title' => 'Wall insulation',
                    'build-year' => 'The house is built in :year, houses with this year off construction often have no cavity wall.',
                    'filled-insulation' => 'You have filled in the following insulation for the wall / facade, you should know more by now. Please adjust the value here',
                    'has-cavity-wall' => 'Does this house have a cavity wall ?',
                    'is-facade-plastered-painted' => 'Is the wall / facade plastered or painted ?',
                ],

                'optional' => [
                    'flushing' => 'Are there wall joints that let go or break out',
                    'if-facade-dirty' => 'Are there wall joints that are contaminated ?',
                    'house-with-same-situation' => 'Houses with the same situation often have the same wall / facade surface',
                    'not-right' => 'Not right ?, enter the correct number here. If you do not know the right value leave this field empty.',
                ],

                'insulation-advice' => [
	                'text' => 'The wall / facade is best insulated in the following way',
	                'cavity-wall' => 'Cavity wall insulation',
	                'facade-internal' => 'Internal wall insulation',
	                'research' => 'More research is needed to find out how the walls are best insulated',
                ],

                'indication-for-costs' => [
                    'title' => 'Indication for costs and benefits for this measure.',
                    'gas-savings' => 'Gas savings',
                    'co2-savings' => 'CO2 savings',
                    'savings-in-euro' => 'Savings in €',
                    'indicative-costs' => 'Indicative cost\'s',
                    'comparable-rate' => 'Comparable rate\'s',
                    'year' => 'Year',
                ],

                'taking-into-account' => [
                    'title' => 'You can take the following maintenance measures into account in the coming years',
                    'sub-title' => 'It is advisable to immediately tackle pieces off wall / facade that are already in a bad condition',
                    'expected-costs' => 'Expected cost\'s for this measure',
                    'explanation-specific-situation' => 'Explanation about the specific situation',
                    'additional-info' => 'Information about the specific information',
                    'year' => 'Year',
                ],
            ],
		],
	],
];