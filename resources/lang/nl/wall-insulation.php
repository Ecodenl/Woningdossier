<?php

return [
    'title' => [
        'help' => '',
        'title' => 'Gevelisolatie',
    ],
    'intro' => [
        'build-year-post-1985' => [
            'help' => '<p>Nog niks beschikbaar</p>',
            'title' => 'Bij woningen met dit bouwjaar is de gevel vaak al tijdens de bouw geïsoleerd',
        ],
        'build-year' => [
            'help' => '',
            'title' => 'Het huis is gebouwd in :year.',
        ],
        'build-year-post-1930' => [
            'help' => '<p>Nog niks beschikbaar</p>',
            'title' => 'Woningen met dit bouwjaar hebben vaak een spouwmuur',
        ],
        'build-year-pre-1930' => [
            'help' => '<p>Nog niks beschikbaar</p>',
            'title' => 'Woningen met dit bouwjaar hebben vaak geen spouwmuur',
        ],
        'filled-insulation' => [
            'help' => '<p><span style="font-family: helvetica, arial, sans-serif;">Hier ziet u wat u bij &ldquo;Algemene gegevens van de woning&rdquo; over de aanwezigheid van gevelisolatie hebt aangegeven. Mocht u dit willen veranderen, dan kunt u dat in dit veld doen. </span></p>
<p><span style="font-family: helvetica, arial, sans-serif;">Let wel: Aanpassingen die u hier doet zullen ook op de pagina &ldquo;Algemene gegevens&rdquo; mee veranderen. </span></p>
<p><span style="font-family: helvetica, arial, sans-serif;">Als u aangeeft dat er wel gevelisolatie aanwezig is wordt er geen besparing uitgerekend! Hoe veel u kunt besparen hangt namelijk af van de dikte en kwaliteit van de isolatielaag. </span></p>
<p><span style="font-family: helvetica, arial, sans-serif;">Voor het uitrekenen van de daadwerkelijke besparing bij het na- isoleren van een reeds ge&iuml;soleerde gevel is aanvullend en gespecialiseerd advies nodig. Neem hiervoor contact op met uw energieco&ouml;peratie.</span></p>',
            'title' => 'De huidige situatie voor deze maatregel:',
        ],
        'has-cavity-wall' => [
            'help' => '<p>Hierboven wordt op basis van woningtype en bouwjaar een advies gegeven of uw woning wel of geen spouwmuur heeft. U kunt deze waarde aanpassen als uw situatie hiervan afwijkt. Als niet te achterhalen is of het huis een spouwmuur heeft, kan een bedrijf of een adviseur nader onderzoek doen. Vul in dat geval hier "onbekend" in. Er wordt dan gerekend alsof er een spouwmuur aanwezig is.</p>',
            'title' => 'Heeft deze woning een spouwmuur?',
        ],
        'is-facade-plastered-painted' => [
            'help' => '<p><span style="font-family: helvetica, arial, sans-serif;">Bij gestuukte of geverfde spouwmuren heeft na- isolatie een hoger risico op vorstschade en vochtproblemen. Een gespecialiseerd bedrijf of adviseur kan u verder adviseren of na-isoleren mogelijk is.</span></p>',
            'title' => 'Is de gevel gestuct of geverfd ?',
        ],
        'surface-paintwork' => [
            'help' => '<p>Als de gevel gestuukt of geschilderd is kunt u hier aangeven om hoeveel m2 het gaat. Er wordt dan een kostenindicatie berekend voor het reguliere onderhoud bij deze geveloppervlaktes.</p>',
            'title' => 'Wat is de oppervlakte van de geschilderde gevel?',
        ],
        'damage-paintwork' => [
            'help' => '<p>Als de gevel gestuukt of geschilderd is kunt u hier aangeven in welke staat het stuukwerk / schilderwerk is. Deze informatie wordt gebruikt om u een indicatie over het tijdstip te geven wanneer onderhoud nodig zou kunnen zijn.</p>
<p>We hebben met de volgende waardes gerekend:</p>
<p><strong>Nee</strong> = volgende schilderbeurt over 15 jaar</p>
<p><strong>Een beetje</strong> = volgende schilderbeurt over 7 jaar</p>
<p><strong>Ja</strong> = gevelschilderwerk direct herstellen</p>',
            'title' => 'Is er schade aan het gevelschilderwerk?',
        ],
    ],
    'optional' => [
        'title' => [
            'help' => '',
            'title' => 'Optioneel: Vragen over de staat van onderhoud van de gevel',
        ],
        'flushing' => [
            'help' => '<p>Dit veld is optioneel, u kunt hier een prijsindicatie voor het herstellen van uw gevelvoegwerk uit laten rekenen. Als het niet van toepassing is of u dit niet wilt laten berekenen laat de keuzelijst op &ldquo;Nee&rdquo; staan. U kunt aangeven of er slecht voegwerk aanwezig is en om hoeveel oppervlakte het gaat. Het gaat hierbij alleen om de m2 die gerepareerd moeten worden.</p>',
            'title' => 'Zijn er voegen die loslaten of uitgebroken zijn?',
        ],
        'is-facade-dirty' => [
            'help' => '<p>Dit veld is optioneel, u kunt hier een prijsindicatie voor het reinigen en impregneren van uw gevels laten uitrekenen. U kunt aangeven of er vervuilde gevels aanwezig zijn en om hoeveel oppervlakte het gaat. Het gaat hierbij alleen om de m2 die gereinigd moeten worden.</p>',
            'title' => 'Is de gevel vervuild (aanslag op de stenen)?',
        ],
        'facade-surface' => [
            'help' => '<p>Deze waarde wordt automatisch ingevuld op basis van bouwjaar en woningtype. Het gaat hierbij om het netto oppervlakte van de gesloten gevel, alle openingen of andere gevelelementen worden niet meegeteld. U kunt deze waarde aanpassen.</p>',
            'title' => 'Geveloppervlakte van de woning',
        ],
        'insulated-surface' => [
            'help' => '<p>Indien niet de hele gevel ge&iuml;soleerd moet worden (bijvoorbeeld bij binnengevelisolatie) kunt u hier de te isoleren geveloppervlakte invullen.</p>',
            'title' => 'Te isoleren oppervlakte',
        ],
    ],
    'insulation-advice' => [
        'text' => [
            'help' => '<p>In dit veld wordt advies gegeven hoe de gevel ge&iuml;soleerd zou kunnen worden. Hierbij zijn er voorlopig nog maar twee opties: Spouwmuurisolatie en Binnengevelisolatie. Binnenkort wordt hier nog Buitengevelisolatie aan toe gevoegd.</p>',
            'title' => 'Het volgende wordt geadviseerd:',
        ],
    ],
    'taking-into-account' => [
        'title' => [
            'help' => '',
            'title' => 'U kunt de komende jaren met de volgende onderhoudsmaatregelen rekening houden:',
        ],
        'sub-title' => [
            'help' => '<p>Geen helptext</p>',
            'title' => 'Het is aan te raden om stukken gevel die nu al heel slecht zijn meteen aan te pakken.',
        ],
        'repair-joint' => [
            'help' => '<p>Als u hierboven de vragen over reparatie voegwerk hebt ingevuld wordt hier een indicatie over de te verwachten reparatiekosten gegeven.</p>',
            'title' => 'Reparatie voegwerk',
            'label' => 'Reperatie voegwerk',
            'year' => [
                'title' => 'Jaar voegwerk',
            ],
        ],
        'clean-brickwork' => [
            'help' => '<p>Als u hierboven de vragen over gevelreiniging hebt ingevuld wordt hier een indicatie over de te verwachten reinigingskosten gegeven.</p>',
            'title' => 'Reinigen metselwerk',
            'label' => 'Reinigen metselwerk',
            'year' => [
                'title' => 'Jaar gevelreiniging',
            ],
        ],
        'impregnate-wall' => [
            'help' => '<p>Als u hierboven de vragen over gevelreiniging hebt ingevuld wordt hier een indicatie over de te verwachten kosten gegeven. Ervan uitgaande dat een gevel die net gereinigd is ook ge&iuml;mpregneerd moet worden.</p>',
            'title' => 'Impregneren gevel',
            'label' => 'Impregneren gevel',
            'year' => [
                'title' => 'Jaar gevel impregneren',
            ],
        ],
        'wall-painting' => [
            'help' => '<p>Als u hierboven de vragen over gevelschilderwerk en stukwerk hebt ingevuld wordt hier een indicatie over de te verwachten reparatiekosten gegeven.</p>',
            'title' => 'Gevelschilderwerk op stuk of metselwerk',
            'label' => 'Gevelschilderwerk op stuk of metselwerk',
            'year' => [
                'title' => 'Jaar gevelschilderwerk',
            ],
        ],
        'additional-info' => [
            'help' => '<p>Hier kunt u opmerkingen over uw specifieke situatie vastleggen, bijvoorbeeld voor een gesprek met een energiecoach of een uitvoerend bedrijf.</p>',
            'title' => 'Toelichting op de specifieke situatie',
        ],
    ],
    'indication-for-costs' => [
        'title' => [
            'help' => '',
            'title' => 'Indicatie voor kosten en baten voor deze maatregel',
        ],
    ],
    'alerts' => [
        'description' => [
            'title' => 'Let op: geverfde of gestukte gevels kunnen helaas niet voorzien worden van spouwmuurisolatie',
        ],
    ],
    'wall-insulation-research' => 'Er is nader onderzoek nodig hoe de gevel het beste geïsoleerd kan worden',
    'facade-wall-insulation' => 'Binnengevelisolatie',
    'cavity-wall-insulation' => 'Spouwmuurisolatie',
    'comment' => [
        'title' => 'Toelichting op Gevelisolatie',
    ],
    'index' => [
        'costs' => [
            'gas' => [
                'title' => 'Gasbesparing',
                'help' => '<p>De besparing wordt berekend op basis van de door u ingevoerde woningkenmerken:</p><p><strong>- vierkante meters te isoleren geveloppervlakte</strong><br><strong>- type gevelisolatie</strong><br><strong>- gemiddelde stooktemperatuur in de woning (zoals bij gebruikersgedrag ingevoerd)*</strong><br><strong>- uw daadwerkelijk energiegebruik**.</strong></p><p>&nbsp;</p><p><span style="font-size: 10pt;">*De berekeningen zijn gekoppeld aan de binnentemperatuur. Bij een realistische invoer van de huidige verwarmingssituatie zal de besparing afgestemd zijn op het daadwerkelijke verbruik.</span></p><p><span style="font-size: 10pt;">**Per maatregel is er per woningtype een maximaal mogelijke besparingspercentage opgegeven. Bij gevelisolatie is bijvoorbeeld voor een tussenwoning maximaal 20 % besparing op het daadwerkelijke gasverbruik voor verwarming mogelijk. Hierdoor wordt voorkomen dat de optelsom van alle besparingen boven uw huidige gasverbruik uitkomt.</span></p><p>&nbsp;</p>',
            ],
            'co2' => [
                'title' => 'CO2 Besparing',
                'help' => '<p>Gerekend wordt met 1,88 kg/m3 gas (bron: Milieucentraal)</p>',
            ],
        ],
        'interested-in-improvement' => [
            'title' => 'Uw interesse in deze maatregel',
            'help' => 'Hier ziet u wat u bij “Algemene gegevens” over uw interesse voor gevelisolatie hebt aangegeven. Mocht u dit willen veranderen, dan kunt u dat in dit veld doen. Let wel: Aanpassingen die u hier doet zullen ook op de pagina “Algemene gegevens” mee veranderen.',
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
            'title' => 'Toelichting op de specifieke situatie',
            'help' => 'Hier kunt u opmerkingen over uw specifieke situatie vastleggen, bijvoorbeeld voor een gesprek met een energiecoach of een uitvoerend bedrijf.',
        ],
    ],
];
