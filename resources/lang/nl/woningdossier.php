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
	    'admin' => [
	        'choose-roles' => [
	        	'header' => 'Als welke rol wilt u doorgaan ?',
                'text' => 'Kies hier met welke rol u wilt doorgaan, u kunt dit op elk moment veranderen'
            ],
	'coach' => [
                'side-nav' => [
                    'label' => 'Coach menu',
                    'index' => 'Home',
                    'buildings' => 'Gebouwen',
                    'messages' => 'Berichten',
                ],

                'index' => [
                    'header' => 'Welkom op het coach panel',
                    'text' => 'Alle gebouwen waar u toegang tot heeft, u kunt op de pagina voor gebouwen acties uitvoeren hierop.',

                    'table' => [
                        'columns' => [
                            'street' => 'Straatnaam',
                            'city' => 'Stad',
                            'owner' => 'Eigenaar',
                            'actions' => 'Acties',
                        ],
                    ],
                ],

                'buildings' => [
                    'index' => [
                        'table' => [
                            'columns' => [
                                'street' => 'Straatnaam',
                                'city' => 'Stad',
                                'owner' => 'Eigenaar',
                                'actions' => 'Acties',
                            ],
                        ],
                    ],
                    'header' => 'Gebouwen waar u toegangt tot heeft',
                ]
            ],

            'cooperation' => [
                'coordination' => [
                    'header' => 'Welkom',
                    'text' => 'U kunt hier verschillende dingen doen.'
                ],
                'cooperation-admin' => [
                    'header' => 'Welkom',
                    'text' => 'U kunt hier verschillende dingen doen.',



                ],

                'coordinator' => [
                    'side-nav' => [
                        'reports' => 'Rapporten',
                        'label' => 'Coördinator menu',
                        'home' => 'Home',
                        'assign-roles' => 'Rollen toewijzen',
                        'coach' => 'Coaches',
                        'add-user' => 'Voeg Coach / Bewoner toe'
                    ],
                    'reports' => [
                        'title' => 'Rapportages',
                        'description' => 'Rapportage downloads',

                        'download' => [
                            'by-year' => 'Actieplan per jaar',
                            'by-measure' => 'Actieplan per maatregel',
                        ],
                        'csv-columns' => [
                            'first-name' => 'Voornaam',
                            'last-name' => 'Achternaam',
                            'email' => 'Email',
                            'phonenumber' => 'Telefoonnummer',
                            'mobilenumber' => 'Mobiel nummer',
                            'street' => 'Straat',
                            'house-number' => 'Huis nummer',
                            'city' => 'Woonplaats',
                            'zip-code' => 'Postcode',
                            'country-code' => 'Landcode',
                        ],
                    ],

                    'assign-roles' => [
                        'index' => [
                            'header' => 'Overzicht gebruikers - rollen toewijzen',

                            'table' => [
                                'columns' => [
                                    'first-name' => 'Voornaam',
                                    'last-name' => 'Achternaam',
                                    'email' => 'E-mail adres',
                                    'role' => 'Huidige rollen van gebruiker',
                                    'actions' => 'Acties'
                                ]

                            ],
                        ],
                        'edit' => [
                            'header' => 'Verander rollen voor :firstName :lastName',

                            'form' => [
                                'first-name' => 'Voornaam',
                                'last-name' => 'Achternaam',
                                'roles' => 'Rol toewijzen aan gebruiker',
                                'email' => 'E-mail adres',
                                'role' => 'Koppel rol aan de nieuwe gebruiker',
                                'select-role' => 'Selecteer een rol...',
                                'password' => [
                                    'header' => 'Wachtwoord instellen',
                                    'label' => 'Wachtwoord',
                                    'placeholder' => 'Wachtwoord invullen...',
                                    'help' => 'U kunt het wachtwoord leeg laten, de gebruiker kan deze dan zelf invullen'
                                ],

                                'submit' => 'Rollen wijzigen',
                            ],
                        ],
                        'update' => [
                            'success' => 'Rollen zijn bijgewerkt'
                        ],
                    ],
                    'coach' => [
                        'index' => [
                            'header' => 'Overzicht van alle coaches voor uw coöperatie',

                            'table' => [
                                'columns' => [
                                    'first-name' => 'Voornaam',
                                    'last-name' => 'Achternaam',
                                    'email' => 'E-mail adres',
                                    'role' => 'Huidige rollen van gebruiker',
                                    'total-houses' => 'Totale woningen',
                                    'actions' => 'Acties'
                                ]

                            ],
                        ],

                        'create' => [
                            'form' => [
                                'first-name' => 'Voornaam',
                                'last-name' => 'Achternaam',
                                'roles' => 'Rol toewijzen aan gebruiker',
                                'email' => 'E-mail adres',
                                'role' => 'Koppel rol aan de nieuwe gebruiker',
                                'select-role' => 'Selecteer een rol...',
                                'password' => [
                                    'header' => 'Wachtwoord instellen',
                                    'label' => 'Wachtwoord',
                                    'placeholder' => 'Wachtwoord invullen...',
                                    'help' => 'U kunt het wachtwoord leeg laten, de gebruiker kan deze dan zelf invullen'
                                ],

                                'submit' => 'Gebruiker aanmaken',
                            ]
                        ],

                        'store' => [
                            'success' => 'Gebruiker is met toevoegd',
                        ],
                        'destroy' => [
                            'success' => 'Gebruiker is verwijderd'
                        ]
                    ],
                    'index' => [
                        'header' => 'Coordinator hoofdpagina - overzicht van alle gebruikers voor uw cooperatie',
                        'text' => 'Een overzicht van alle <strong>gebruikers</strong> van uw huidige cooperatie',

                        'table' => [
                            'columns' => [
                                'first-name' => 'Voornaam',
                                'last-name' => 'Achternaam',
                                'email' => 'E-mail adres',
                                'role' => 'Huidige rollen van gebruiker',
                                'actions' => 'Acties'
                            ]

                        ],
                    ],
                ],
            ],

	        'navbar' => [
	            'current-role' => 'Uw huidge rol:',
	        	'reports' => 'Rapportages',
                'example-buildings' => 'Example buildings'
            ],

        ],

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
                'panel-title' => 'Disclaimer',
                'description' => '<b>Beste gebruiker,</b><br><br>Het Hoomdossier is ontwikkeld door Coöperatie Hoom om u te ondersteunen bij het verduurzamen van uw woning. Het Hoomdossier bevindt zich nog in de testfase en wordt op dit moment op vier plekken in Nederland getest. Om de software zo snel mogelijk te verbeteren en nog beter bruikbaar te maken stellen we uw feedback zeer op prijs. Mocht u fouten tegenkomen, opmerkingen of wensen hebben, klik dan <a href="https://form.jotformeu.com/81345355694363" target="_blank">hier.</a> <br><br>In het Hoomdossier worden gegevens over uw woning, uw energieverbruik en uw gebruiksgedrag opgeslagen. De gegevens worden gebruikt om u te adviseren welke energiebesparende maatregelen u kunt nemen in uw woning en wat de indicatieve kosten en baten van deze maatregelen zijn. <br><br>Op basis van deze berekeningen kunt u zelf een stappenplan voor de komende jaren opstellen en bijhouden. De gegevens uit dit stappenplan worden gedeeld met uw coöperatie om u zo goed mogelijk bij de uitvoering van het plan te helpen. Onderaan dit tekst kunt u zien welke gegevens uit het Hoomdossier gedeeld worden. Alle overige gegevens zijn uitsluitend via uw account voor u zelf zichtbaar en aanpasbaar.<br><br><b>Dit kunt u van het Hoomdossier verwachten:</b><ul><li>De huidige situatie van uw woning in beeld brengen.</li><li>Per maatregel wordt een indicatie gegeven over de te verwachten kosten en baten.</li><li>U kunt een actieplan voor de komende jaren opstellen en deze bijhouden.</li><li>Er worden ook een aantal onderhoudsmaatregelen meegenomen, zoals schilderwerk en het vervangen van de cv-installatie.</li><li>Er wordt een samenvatting van het actieplan naar de coöperatie gestuurd.</li> Met deze gegevens kan de coöperatie bijvoorbeeld collectieve inkoopacties organiseren.</li></ul><b>Dit kan het Hoomdossier niet:</b><ul><li>Advies geven over specifieke situaties (bijvoorbeeld over welk type warmtepomp er moet komen of bouwkundig advies geven).</li><li>Het (definitief) vaststellen of een bepaalde maatregel technisch mogelijk en uitvoerbaar is.</li><li>Advies geven over comfortverbetering.</li><li>Tot op de komma de kosten en baten van een maatregel uitrekenen. De berekeningen zijn een benadering.</li></ul><br>Het Hoomdossier maakt gebruik van formules en vergelijkingen die een benadering zijn van de werkelijkheid. Hoewel het Hoomdossier dus wel inzicht geeft in de potentiele impact van energiebesparende maatregelen, kan het een persoonlijk advies op maat niet vervangen. In overleg met uw coöperatie kunt u het Hoomdossier gebruiken als basis voor een Basisadviesgesprek of een professioneel advies. <br><br>Er kan geen garantie aan de resultaten van het Hoomdossier ontleend worden ten aanzien van de daadwerkelijke energieprestaties, berekende energiegebruik of besparingen. <u>De essentie van het rekenen met het Hoomdossier is het krijgen van inzicht in consequenties van het nemen van maatregelen.</u><br><br>Gegevens die met de coöperatie gedeeld worden:<br><br><ul><li>Voornaam en achternaam<li>Straat en huisnummer</li><li>Postcode plaats</li><li>E-mail adres</li><li>Telefoonnummer</li></ul><br>Van alle maatregelen die in uw actieplan staan worden de volgende gegevens gedeeld:<ul><li>Naam maatregel</li><li>Interesse (Ja/nee)</li><li>Indicatieve kosten</li><li>Indicatieve besparing gas</li><li>Indicatieve besparing elektra</li><li>Indicatieve besparing €</li><li>Geadviseerd uitvoeringsjaar</li><li>Zelf ingevuld uitvoeringsjaar</li></ul>',
            ],
        ],
        'help' => [
            'title' => 'Help',
            'help' => [
                'help-with-filling-tool' => 'Ik wil hulp bij het invullen',
                'no-help-with-filling-tool' =>  'Ik ga zelf aan de slag',
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

		    'index' => [
		        'header' => 'Mijn account',
                'text' => 'U kunt vanaf hier naar uw instellingen gaan om uw account te wijzigen, voortgang te resetten of om het account te verwijderen. Of u kunt naar uw berichten gaan om deze te zien.',

                'settings' => 'Instellingen <span class="glyphicon glyphicon-cog">',
                'messages' => 'Berichten <span class="glyphicon glyphicon-envelope">',
            ],

            'messages' => [
                'navigation' => [
                    'inbox' => 'Inbox',
                    'requests' => 'Uw aanvragen',

                    'conversation-requests' => [
                        'request' => 'Coachgesprek aanvragen',
                        'update-request' => 'Coachgesprek aanvraag bijwerken',
//                        'disabled' => 'U heeft al antwoord op uw aanvraag, als deze aanvraag is afgehandeld kunt u een nieuwe indienen'
                        'disabled' => 'Niet beschikbaar'
                    ],

                ],
                'index' => [
                    'header' => 'Mijn berichten',

                    'chat' => [
                        'conversation-requests-consideration' => [
                            'title' => 'Uw aanvraag is in behandeling',
                            'text' => 'Uw aanvraag is in behandeling, er word op het moment voor u een coach uitgekozen die het best bij uw situatie past.',
                        ],
                        'no-messages' => [
                            'title' => 'Geen berichten',
                            'text' => 'Er zijn nog geen berichten. Deze zullen hier verschijnen nadat u antwoord heeft gekregen op een aanvraag voor een coachgesprek of offerte.',
                        ]
                    ],

                ],

                'edit' => [
                    'header' => 'Berichten',

                    'chat' => [
                        'input' => 'Type uw antwoord hier...',
                        'button' => 'Verstuur',
                    ],
                ],

                'requests' => [
                    'index' => [

                        'header' => 'Mijn aanvragen',

                        'chat' => [
                            'conversation-requests-consideration' => [
                                'title' => 'Uw aanvraag is in behandeling',
                                'text' => 'Uw aanvraag is in behandeling, er wordt een coach voor u uitgekozen die het best bij uw situatie past.',
                            ],
                            'no-messages' => [
                                'title' => 'Geen berichten',
                                'text' => 'Er zijn nog geen berichten. Deze zullen hier verschijnen nadat u antwoord heeft gekregen op een aanvraag voor een coachgesprek of offerte.',
                            ]
                        ],
                    ],
                    'update' => [
                        'success' => 'Uw aanvraag is bijgewerkt. u kunt <strong><a href=":url">hier uw berichten bekijken</a> </strong> ',
                    ],
                ],
            ],


            'settings' => [
                'form' => [
                    'index' => [
                        'header' => 'Mijn account',
                        'submit' => 'Update',
                    ],
                    'store' => [
                        'success' => 'Gegevens succesvol gewijzigd',
                    ],
                    'reset-file' => [
                        'header' => 'Uw dossier verwijderen',
                        'description' => '<b>Let op:</b> dit verwijdert alle gegevens die zijn ingevuld bij de verschillende stappen!',
                        'label' => 'Reset mijn dossier',
                        'submit' => 'Reset',
                        'are-you-sure' => 'Letop, dit verwijderd alle gegevens die zijn ingevuld bij de veschillende stappen. Weet u zeker dat u wilt doorgaan ?',
                        'success' => 'Uw gegevens zijn succesvol verwijderd van uw account',
                    ],
                    'destroy' => [
                        'header' => 'Account verwijderen',
                        'label' => 'Mijn account verwijderen',
                        'submit' => 'Verwijderen',
                    ],
                ],
            ],
			'cooperations' => [
				'form' => [
					'header' => 'Mijn coöperaties',
				],

			],

		],
        'conversation-requests' => [

            'index' => [
                'header' => 'Actie ondernemen',
                'text' => 'De gegevens worden uitsluitend door de coöperatie gebruikt om u in uw bewonersreis te ondersteunen. Uw persoonlijke gegevens worden niet doorgegeven aan derden. Meer informatie over de verwerking van uw data en wat we ermee doen kunt u vinden in ons privacy statement.',

                'form' => [
                    'no-measure-application-name-title' => 'Gesprek aanvragen',
                    'title' => 'Actie ondernemen met :measure_application_name',
                    'allow_access' => 'Ik geef toesteming aan :cooperation om de gegevens uit mijn Hoomdossier in te zien en in overleg met mij deze gegevens aan te passen.',
                    'are-you-sure' => 'Weet u zeker dat u de Coöperatie geen toegang wilt geven tot uw dossier?',
                    'action' => 'Actie',
                    'take-action' => 'Actie ondernemen',
                    'message' => 'Nadere toelichting op uw vraag',
                    'submit' => 'Opsturen <span class="glyphicon glyphicon-envelope"></span>',

                    'selected-option' => 'Waar kunnen we u bij helpen?:',
                    'options' => [
                        \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
                        \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie gewenst',
                        \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...',
                    ],
                ],
            ],

            'edit' => [
                'header' => 'Bewerk uw huidige :request_type',
                'text' => 'De gegevens worden uitsluitend door de coöperatie gebruikt om u in uw bewonersreis te ondersteunen. Uw persoonlijke gegevens worden niet doorgegeven aan derden. Meer informatie over de verwerking van uw data en wat we ermee doen kunt u vinden in ons privacy statement.',

                'form' => [

                    'allow_access' => 'Ik geef toesteming aan :cooperation om de gegevens uit mijn Hoomdossier in te zien en in overleg met mij deze gegevens aan te passen.',
                    'are-you-sure' => 'Weet u zeker dat u de Coöperatie geen toegang wilt geven tot uw dossier?',
                    'action' => 'Actie',
                    'take-action' => 'Actie ondernemen',
                    'message' => 'Uw bericht aan de cooperatie',
                    'update' => 'Aanvraag bijwerken <span class="glyphicon glyphicon-envelope"></span>',

                    'selected-option' => 'Waar kunnen we u bij helpen?:',

                    \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
                    \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie gewenst',
                    \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...',

                    'options' => [
                        \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
                        \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie gewenst',
                        \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...'
                    ],
                ],
            ],

		'store' => [
                'success' => 'Uw aanvraag is sucessvol verstuurd, u krijgt zo spoedig mogelijk antwoord. u kunt <strong><a href=":url"">hier uw berichten bekijken</a> </strong> ',
            ],
            'update' => [
                'success' => 'Uw aanvraag is sucessvol bijgewerkt, u krijgt zo spoedig mogelijk antwoord. u kunt <strong><a href=":url"">hier uw berichten bekijken</a> </strong> ',
                'warning' => 'U heeft al een :request_type open staan, u kunt niet meerdere :request_type open hebben staan. Deze moet eerst worden afgehandeld zijn, u kunt deze hier wel bewerken.'
            ],

            'edit-conversation-requests' => 'U kunt uw huidige aanvragen <strong><a href="'.route('cooperation.my-account.messages.requests.index').'">hier bekijken</a></strong> ',

        ],
		'tool' => [

		    'change-interest' => 'U heeft in de eerste stap uw interesse over :item aangegeven, u kunt deze hier veranderen of zo laten.',

			'unit' => [
				'year' => 'jaar',
				'liter' => 'liter',
				'day' => 'dag',
                'pieces' => 'stuks',
				'square-meters' => 'm<sup>2</sup>',
				'cubic-meters' => 'm<sup>3</sup>',
				'co2' => 'CO<sub>2</sub>',
                'kilograms' => 'kg',
                'degrees' => 'graden',
				'kwh' => 'kWh',
				'hours' => 'uren',
			],

			'title' => 'Basisadvies',

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

				'example-building' => [
					'no-match' => 'Er is geen passende voorbeeldwoning',
				],

				'energy-saving-measures' => [
					'title' => 'Wat is de huidige staat van isolatie van uw woning en in welke maatregelen heeft u interesse?',
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
                        'if-yes' => 'Wanneer zijn de huidige panelen geplaatst?',
					],
					'sun-boiler' => 'Zonneboiler',
					'house-ventilation' => [
						'title' => 'Hoe wordt het huis geventileerd?',
						'if-mechanic' => 'Indien mechanisch: wanneer is installatie geplaatst?',
					],
					'additional' => 'Overig',
					'interested' => 'Interesse in verbetering?',

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
					'electricity-consumption-past-year' => 'Wat is het elektragebruik van het afgelopen jaar?',
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
                    'has-cavity-wall' => 'Heeft deze woning een spouwmuur?',
                    'is-facade-plastered-painted' => 'Is de gevel gestuct of geverfd?',
                    'surface-paintwork' => 'Wat is de oppervlakte van de geschilderde gevel?',
                    'damage-paintwork' => 'Is er schade aan het gevelschilderwerk?',
                ],

                'optional' => [
                    'title' => 'Optioneel: Vragen over de staat van onderhoud van de gevel',
                    'flushing' => 'Zijn er voegen die loslaten of uitgebroken zijn?',
                    'if-facade-dirty' => 'Is de gevel vervuild (aanslag op de stenen)?',
	                'facade-surface' => 'Geveloppervlakte van de woning',
	                'wall-surface' => 'Geveloppervlakte',
	                'insulation-wall-surface' => 'Te isoleren geveloppervlakte',
                ],

                'alert' => [
                    'description' => 'Let op, geverfde of gestukte gevels kunnen helaas niet voorzien worden van spouwmuurisolatie',
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
	            'subtitles' => [
	            	'glass-in-lead' => 'Vragen over glas in lood',
		            'place-hr-only-glass' => 'Vragen over vervangen glas',
		            'place-hr-with-frame' => 'Vragen over vervangen kozijn',
	            ],
	            'interested-in' => 'Bent u geïnteresseerd in :measure?',

                'cracking-seal' => [
                    'title' => 'Kierdichting',
                ],
                'current-glass' => 'Wat voor glas is er nu?',
                'heated-rooms' => 'Zijn de kamers verwarmd?',
                'm2' => 'Hoeveel m<sup>2</sup> glas wilt u vervangen?',
                'total-windows' => 'Aantal te vervangen ruiten',

                'moving-parts-quality' => 'Zijn de draaiende delen van ramen en deuren voorzien van kierdichting?',

                'facade-surface' => 'Geveloppervlakte van de woning',
                'windows-surface' => 'Totale raamoppervlakte van de woning',

                'paint-work' => [
                    'title' => 'Vragen over het schilderwerk',
                    'which-frames' => 'Welke kozijnen heeft uw huis?',
                    'other-wood-elements' => 'Welke andere houten bouwdelen zijn aanwezig in uw huis?',
                    'last-paintjob' => 'Wanneer is het schilderwerk voor het laatst gedaan?',
                    'paint-damage-visible' => 'Is verfschade waarneembaar? (barsten / afbladderen / blazen)',
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
                'no-roof' => 'Dit veld is verplicht als u een dak type heeft gekozen',
				'title' => 'Dakisolatie',
				'current-situation' => [
					'bitumen-insulated' => 'Wanneer is het bitumen dak voor het laatst vernieuwd?',
					'title' => 'Huidige situatie',
					'roof-types' => 'Wat voor daktypes zijn aanwezig in uw woning?',
					'main-roof' => 'Wat is het hoofddak?',
					'insulation-flat-roof-surface' => 'Te isoleren oppervlakte plat dak',
					'insulation-pitched-roof-surface' => 'Te isoleren oppervlakte hellend dak',
					'in-which-condition-tiles' => 'In welke staat verkeren de dakpannen?',
					'is-flat-roof-insulated' => 'is het platte dak geïsoleerd?',
					'is-pitched-roof-insulated' => 'is het hellende dak geïsoleerd?',
					'flat-roof-surface' => 'Dakoppervlak van platte dak',
					'pitched-roof' => 'Is het hellende dak geïsoleerd?',
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
					'no' => 'Niet',
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
				'already-efficient' => 'Het vervangen van de huidige ketel zal alleen een beperkte energiebesparing opleveren omdat u al een HR ketel hebt.',

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
                'amount' => 'stuks',
				'peak-power' => 'Piekvermogen per paneel',
				'advice-text' => 'Voor het opwekken van uw huidige elektraverbruik heeft u in totaal ca. :number zonnepanelen in optimale oriëntatie nodig.',
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

			    'options' => [
                    \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION => 'Ondersteuning door een energiecoach',
                    \App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION => 'Meer informatie gewenst',
                    \App\Models\PrivateMessage::REQUEST_TYPE_OTHER => 'Anders...'
                ],
				'warnings' => [
                	'title' => 'Let op!',
                    'check-order' => 'U probeert dakisolatie met vervanging van de dakbedekking te plannen, maar de onderhoudsmaatregel voor het vervangen van de dakpannen of dakbedekking staat uit!',
                    'planned-year' => 'De uitvoeringsjaren van de energiebesparende maatregel en de onderhoudsmaatregel zijn niet gelijk!',
                ],

                'title' => 'Actieplan',
				'description' => 'Op deze pagina ziet u een samenvatting van alle maatregelen die u in het hoomdossier volledig hebt ingevuld. Per maatregel ziet u wat de indicatieve kosten en besparingen zijn.<br><br>Op basis van deze uitkomsten kunt u uw persoonlijke stappenplan voor de komende jaren samenstellen. Hiervoor selecteert u een maatregel in de eerste kolom (“Interesse”) en voert in de laatste kolom (“Planning”) het jaartal in wanneer u deze maatregel uit zou willen voeren.<br><br>Onder aan de pagina wordt dan uw stappenplan weergegeven. Per jaar kunt u zien hoe veel geld u voor onderhoud en energiebesparende maatregelen zou moeten reserveren en wat u aan besparing op uw energierekening in dit jaar zou kunnen verwachten.',
				'energy-saving-measures' => 'Energiebesparende maatregelen',
				'maintenance-measures' => 'Onderhoud',

				'maintenance-plan' => 'Uw persoonlijke meerjarenonderhoudsplan',
				'no-year' => 'Geen jaartal',
                'download' => 'Download hier je actieplan',

                'conversation-requests' => [
                    'take-action' => 'Actie ondernemen',
                    'request' => 'Coachgesprek aanvragen',
                    'update-request' => 'Coachgesprek aanvraag bijwerken',
//                    'disabled' => 'U heeft al antwoord op uw aanvraag, als deze aanvraag is afgehandeld kunt u een nieuwe indienen'
                    'disabled' => 'Niet beschikbaar'
                ],
                'conversation-requests-request' => 'Coachgesprek aanvraag',
                'conversation-requests-request-update' => 'Coachgesprek aanvraag bijwerken',

                'csv-columns' => [
                    'year-or-planned' => 'Jaar / gepland jaar',
                    'interest' => 'Interesse',
                    'measure' => 'Maatregel',
                    'costs' => 'Kosten',
                    'savings-gas' => 'Besparing m3 gas',
                    'savings-electricity' => 'Besparing kWh elektra',
                    'savings-costs' => 'Besparing in euro',
                    'advice-year' => 'Geadviseerd jaar',
                    'planned-year' => 'Planning',
	                'costs-advice-year' => 'Kosten in geadviseerd jaar',
                ],
				'columns' => [
					'more-info' => 'Meer info',
					'interest' => 'Interesse',
                    'measure' => 'Maatregel',
                    'costs' => 'Kosten',
                    'savings-gas' => 'Besparing m<sup>3</sup> gas',
                    'savings-electricity' => 'Besparing kWh elektra',
                    'savings-costs' => 'Besparing in euro',
                    'advice-year' => 'Geadviseerd',
                    'planned-year' => 'Planning',
                    'take-action' => 'Actie',
                    'more-information' => 'Meer info',
                    'quotation' => 'Vraag offerte aan',
                    'help-question' => 'Hulpvraag'
                ],
            ],

            'ventilation-information' => [
                'title' => 'Informatie pagina over ventilatie.',
                'description' => 'Voor uw gezondheid is schone lucht noodzakelijk. Goede ventilatie in uw woning hoort daarbij, maar vaak wordt er te weinig geventileerd. Schimmel, tabaksrook en fijnstof zijn veel voorkomende vervuiling in woningen. Vervuilde lucht in huis versterkt allergieën, luchtwegproblemen en irritatie van slijmvliezen, zeker bij de oudere generatie. Zorg dus voor voldoende ventilatie in uw woning.<br><br>In oude huizen gaat de luchtverversing in veel situaties vanzelf via naden en kieren. Dat is echter niet zo goed voor het comfort en zorgt voor een hoog energiegebruik. Daarom worden de huizen steeds luchtdichter gemaakt en van goede isolatie voorzien. Om een gezond binnenklimaat te bereiken is hierbij een andere vorm van ventilatie nodig. Vraag gestuurd moet vuile lucht het huis uit en schone lucht moet aangevoerd worden. Ventilatielucht opwarmen kost energie, maar het is geen verspilling: het is hard nodig voor uw gezondheid.<br><br>Hieronder leggen we twee veel voorkomende basisprincipes van ventilatie uit en geven een aantal tips voor een goed binnenklimaat.<br><br><h4>Natuurlijke ventilatie</h4>In een huis met natuurlijke ventilatie zit geen mechanisch ventilatiesysteem, dat betekent dat er alleen via natuurlijke weg geventileerd kan worden door ventilatieroosters en bijvoorbeeld ramen of deuren open te zetten. Meestal is er bij oude huizen sprake van een bepaalde basisventilatie door naden, kieren. Het verbeteren van de kierdichtheid vermindert de natuurlijke ventilatie in huis. Bewust of vraag gestuurd ventileren wordt dan de beste weg om zo zuinig mogelijk een gezonde leefomgeving te houden.<br><br><strong>Hoe kunt u luchten</strong>Ventileren kan door ramen en deuren tegen elkaar open te zetten, en de verwarming uit te zetten. We noemen dat luchten of spuien. De in het vertrek aanwezige waterdamp, die anders in de muren zou trekken en tegen de ramen kan condenseren, wordt met de vervuilde lucht afgevoerd. De verse lucht wordt opgewarmd door de warmte die nog in muren en plafond aanwezig is. Dit luchten hoeft niet heel lang te duren. In de winter korter dan in de zomer, afhankelijk van de buitentemperatuur. Lucht de woonvertrekken vooral in de koude perioden kort maar goed, door zoveel mogelijk ramen open te zetten. Een goed tijdstip om woonvertrekken extra te luchten is voor het naar bed gaan, als de kachel lager staat.<br><br><strong>Slaapkamers</strong>Slaapkamers kunnen het beste in de ochtend gelucht worden voor ongeveer 20 minuten (wat korter in de winter, wat langer in de zomer). Zo kan de waterdamp, ontstaan tijdens de nacht afgevoerd worden. Ook voor het slapen gaan is het aan te raden even te luchten. Het is niet aan te raden de ramen in de slaapkamer altijd open of op kiepstand te laten staan, zeker in de winter en bij temperaturen onder de 10 graden. Door het afkoelen van de gevel rondom de ramen kunnen er vochtplekken en schimmel ontstaan. Ventilatieroosters kunnen wel continu open staan omdat de koude lucht hierbij niet langs de muren naar binnen stroomt en deze dus minder koud worden. Als u toch graag bij open raam slaapt zorg er in ieder geval voor dat de slaapkamerdeur gesloten is als het raam op kiep staat. Het mee verwarmen van een onverwarmde kamer door het open laten staan van de deur vormt een groot risico voor condensatie op de koude oppervlaktes.<br><br><strong>Extra ventileren</strong>Houd de deur van de badkamer gesloten, zorg wel voor een rooster in de deur of een spleet onder de deur en zet tijdens het douchen de eventuele ventilatie op de hoogste stand, zodat het vocht snel wordt afgevoerd. Ventileer ook extra tijdens het koken, via een open raam of een afzuigkap. En als er op een moment veel mensen in huis zijn, zet dan een deur of raam open. Ook bij klussen in huis zoals schilderen, is extra ventilatie nodig, ook na afloop, dan verdwijnen vrijgekomen stoffen zoals oplosmiddelen sneller uit uw huis.<br><br><strong>Cv-gebruik in de winter</strong>Tijdens koude periodes (lager dan 10 graden) is het van belang de radiatorkranen door het hele huis iets te openen, ook in de ruimtes waar op dat moment niemand aanwezig is. Door het beperkt mee verwarmen van deze ruimtes stookt u over het algemeen niet minder zuinig. Andere vertrekken worden dan ook sneller warm, en u zult minder last hebben van vochtproblemen.<br><br>Meer tips tegen vocht:<ul><li>Kook met de deksel op de pan, dat kost niet alleen minder energie, maar u zorgt er ook voor dat er minder vocht vrijkomt.</li><li>Droog wasgoed het liefst buiten, of in een wasdroger. Hangt u het binnen, doe dit dan in een goed geventileerde ruimte.</li><li>Maak na het dweilen de vloer droog.</li><li>Stop ventilatieroosters nooit dicht, controleer ook regelmatig de ventilatieroosters onderaan in de gevel, die zorgen voor ventilatie van de kruipruimte.</li><li>Gaat u voor langere tijd weg in de winter, laat dan de verwarming ‘op een laag pitje‘ staan ter voorkoming van condens en schimmelproblemen. Het zorgt er ook voor dat uw waterleiding niet bevriest.</li></ul><br><br><h4>Mechanische afzuigventilator</h4>In een huis met een mechanische afzuiging zorgt een ventilator er voortdurend voor dat vervuilde lucht afgevoerd wordt. Tegelijkertijd komt via open ventilatieroosters schone lucht naar binnen. Om altijd een gezond binnenklimaat te kunnen waarborgen zijn deze ventilatiesystemen erop berekend om het hele jaar continu te draaien. Meestal is er een driestanden schakelaar in de woning aanwezig waarmee de installatie geregeld kan worden.<br><br>Oude ventilatoren gebruiken soms nog wisselstroom en verbruiken voor dezelfde prestatie veel meer elektriciteit en maken meer geluid dan moderne gelijkstroom ventilatoren. De besparing op de gebruikte stroom kan oplopen tot ca. 80 %. Een installateur kan direct beoordelen of u nog een wisselstroom ventilator heeft.<br><br><h4>Aandachtspunten voor het juiste gebruik van het ventilatiesysteem</h4>Een drie-standenschakelaar wordt het beste als volgt gebruikt:<ul><li>Stand 1 is de basisstand, bedoeld om het laagste ventilatieniveau te garanderen, bijvoorbeeld als er langere tijd niemand thuis is.</li><li>Stand 2 is de stand die is aanbevolen bij een normale aanwezigheid van mensen in de woning.</li><li>Stand 3 is bedoeld voor afzuiging tijdens koken en vochtafvoer uit de badkamer.</li><li>Vooral wanneer binnenshuis ook natte was wordt opgehangen, is stand 1 echt onvoldoende voor een adequate afvoer van vochtige lucht.</li><li>De verversing van luncht op de slaapkamers is afhankelijk van de aanzuiging in de badkamer. Let er dus op dat een raam in de badkamer alleen kortstondig open blijft staan na het douchen. Als het badkamerraam veel langer open blijft (bijvoorbeeld op een kierstand) is er nauwelijks of geen luchtverversing meer op de overige (slaap-)kamers van dezelfde verdieping. Houdt het badkamerraam bij voorkeur dicht en de badkamerdeur ook. Laat de ventielen en de kieren onder de deuren hun werk doen. Zo verdwijnt alle overtollige vocht snel naar buiten via de ventilatie i.p.v. naar de overige kamers en blijft verversing van de lucht in alle kamers gegarandeerd.</li></ul><br><br><h4>Nieuwe vormen van ventilatiesystemen</h4>Nieuwere ventilatiesystemen kunnen beter geregeld worden. Dit kan bijvoorbeeld op winddruk of met sensoren die continu in de woning of de hoeveelheid vocht en CO<sub>2</sub> meten.<br>Daarbij bestaan er systemen die warmte terug kunnen winnen uit de afgevoerde vervuilde lucht. De terug gewonnen warmte kan gebruikt worden voor het opwarmen van verse binnenkomende lucht of voor het verwarmingssysteem.<br>Er zijn installaties voor de hele woning en apparaten die geschikt zijn voor een enkele ruimte.<br><br>Meer informatie kunt u vinden op onze maatregelbladen hieronder of bij milieucentraal: <a href="https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/ventileren/">https://www.milieucentraal.nl/energie-besparen/energiezuinig-huis/ventileren/</a>',

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
                'description' => 'Een warmtepomp zorgt op een milieuvriendelijke manier voor verwarming van uw huis en warm water in de douche en keuken. Het is een duurzaam alternatief voor uw cv-ketel op gas: uw CO2-uitstoot voor verwarming daalt met zo\'n 50 tot 60 procent! Bovendien kunt u bij aankoop subsidie krijgen en gaat uw energierekening omlaag.<br><br><strong>Wat is een warmtepomp?</strong><br> Een warmtepomp is een onderdeel van een centrale verwarmingsinstallatie en zorgt ervoor dat het verwarmingswater wordt verwarmd en naar de laagtemperatuur verwarmingselementen zoals bijvoorbeeld vloerverwarming wordt gepompt. Meestal zorgt de warmtepomp ook voor warmtapwater, voor o.a. douchen en afwassen. We spreken dan van een combiwarmtepomp. Als de warmtepomp gebruikt wordt naast een cv-ketel die de piekvraag oplost, spreken we van een hybride- warmtepomp. <br><br><strong>Welke varianten zijn er?</strong><br>Warmtepompen zijn in verschillende soorten en maten verkrijgbaar. Belangrijk is welke energiebron wordt toegepast. Dat kan de bodem of de buitenlucht zijn. Het is belangrijk om een warmtepomp te kiezen die past bij uw woning. Hoe groter uw huis, hoe meer capaciteit er nodig is. Bij een combiwarmtepomp is daarnaast de CW-waarde belangrijk. Hoe hoger deze waarde, hoe meer warmtapwater de warmtepomp kan produceren.<br><br><strong>Hoeveel kan ik besparen?</strong><br>De rekenmethodiek voor het berekenen van de kosten en baten binnen het hoomdossier is op dit moment nog in ontwikkeling. Binnenkort kunt u hier terecht voor een indicatie wat een warmtepomp in uw situatie aan besparing op kan leveren.<br><br>Bij vragen over warmtepompen kunt u terecht bij uw coöperatie.',
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
                        'warm-tapwater' => 'Warmtegebruik voor warm tapwater',
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
