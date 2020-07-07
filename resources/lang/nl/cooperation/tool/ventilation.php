<?php

return [

    'index' => [
        'intro' => [
            'natuurlijke-ventilatie' => 'U heeft aangegeven dat er in de woning natuurlijke ventilatie aanwezig is. In een huis met natuurlijke ventilatie zit geen mechanisch ventilatiesysteem, dat betekent dat er alleen via natuurlijke weg geventileerd kan worden door ventilatieroosters en bijvoorbeeld ramen of deuren open te zetten. Meestal is er bij oude huizen sprake van een bepaalde basisventilatie door naden, kieren. Het verbeteren van de kierdichtheid vermindert de natuurlijke ventilatie in huis. Bewust of vraag gestuurd ventileren wordt dan de beste weg om zo zuinig mogelijk een gezonde leefomgeving te houden.',
            'mechanische-ventilatie' => 'U heeft aangegeven dat er in de woning Mechanische afzuig ventilatie aanwezig is. In een huis met een centrale mechanische afzuiging zorgt een ventilator er voortdurend voor dat vervuilde lucht afgevoerd wordt. Tegelijkertijd komt via open ventilatieroosters schone lucht naar binnen. Om altijd een gezond binnenklimaat te kunnen waarborgen zijn deze ventilatiesystemen erop berekend om het hele jaar continu te draaien. Regeling gebeurt dan met een drie standen schakelaar of met CO2- of vochtsensoren.',
            'gebalanceerde-ventilatie' => 'U heeft aangegeven dat er in de woning centrale gebalanceerde ventilatie aanwezig is. Bij balansventilatie brengt het ventilatiesysteem de aanvoer en de afvoer van lucht in een gebouw met elkaar in balans: evenveel verse gefilterde buitenlucht wordt aangevoerd naar de woon- en slaapkamers als er vervuilde en vochtige binnenlucht wordt afgevoerd uit de keuken, de badkamer en het toilet. Om altijd een gezond binnenklimaat te kunnen waarborgen zijn deze ventilatiesystemen erop berekend om het hele jaar continu te draaien. Regeling gebeurt dan met een drie standen schakelaar of met CO2- of vochtsensoren.',
            'decentrale-mechanische-ventilatie' => 'U heeft aangegeven dat er in de woning decentrale mechanische ventilatie aanwezig is. Decentrale balansventilatie heeft geen luchtkanalen en centrale ventilator nodig: de ventielen, ventilatoren en eventueel warmteterugwinunit zijn ingebouwd in een (decentraal) apparaat dat tegen een buitenmuur geplaatst is. Dat wil zeggen dat in huis verschillende units via een eigen kanaal verse lucht aanvoeren en vervuilde lucht afvoeren. Het voordeel van zo\'n decentraal systeem is dat er geen luchtkanalen in huis nodig zijn. Decentrale ventilatie units kunnen in combinatie met radiatoren of los geplaatst worden.',
        ],
        'costs' => [
            'gas' => [
                'title' => 'Gasbesparing',
                'help' => '<p>De besparing wordt berekend op basis van de door u ingevoerde woningkenmerken (hoeveelheden, isolatiewaarde, gebruikersgedrag).</p>',
            ],
            'co2' => [
                'title' => 'CO2 Besparing',
                'help' => '<p>Gerekend wordt met 1,88 kg/m3 gas (bron: Milieucentraal)</p>',
            ]
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

        'how'              => [
            'title' => 'Hoe komt de verse lucht nu binnen?',
            'help'  => '',
        ],
        'living-situation' => [
            'title' => 'Onderstaand kunt u aanvinken wat in uw woning verder van toepassing is. Meerdere opties zijn mogelijk.',
            'help'  => '',
        ],
        'usage'            => [
            'title' => 'Hoe gebruikt u de ventilatie unit? Meerdere opties zijn mogelijk.',
            'help'  => 'Dit is de helptekst',
        ],
        'comment'          => [
            'title' => 'Toelichting op de specifieke situatie',
            'help'  => 'Dit is de helptekst',
        ],

        'specific-situation' => [
            'title' => 'Toelichting op de specifieke situatie',
            'help' => 'Hier kunt u opmerkingen over uw specifieke situatie vastleggen, bijvoorbeeld voor een gesprek met een energiecoach of een uitvoerend bedrijf.'
        ],

        'living-situation-warnings' => [
            'dry-laundry' => 'Ventileer extra als de was te drogen hangt, door indien aanwezig de ventilator op de hoogste stand te zetten of een raam open te doen. Hang de was zoveel mogelijk buiten te drogen.',
            'fireplace' => 'Zorg voor extra ventilatie tijdens het stoken van open haard of houtkachel, zowel voor de aanvoer van zuurstof als de afvoer van schadelijke stoffen. Zet bijvoorbeeld een (klep)raam open.',
            'combustion-device' => 'Zorg bij een open verbrandingstoestel in ieder geval dat er altijd voldoende luchttoevoer is. Anders kan onvolledige verbranding optreden waarbij het gevaarlijke koolmonoxide kan ontstaan.',
            'moisture' => 'Wanneer u last heeft van schimmel of vocht in huis dan wordt geadviseerd om dit door een specialist te laten beoordelen.'
        ],

        'usage-warnings' => [
            'sometimes-off' => 'Laat de ventilatie unit altijd aan staan, anders wordt er helemaal niet geventileerd en hoopt vocht en vieze lucht zich op. Trek alleen bij onderhoud of in geval van een ramp (als de overheid adviseert ramen en deuren te sluiten) de stekker van de ventilatie-unit uit het stopcontact.',
            'no-maintenance' => 'Laat iedere 2 jaar een onderhoudsmonteur langskomen, regelmatig onderhoud van de ventilatie-unit is belangrijk. Kijk in de gebruiksaanwijzing hoe vaak onderhoud aan de unit nodig is.',
            'filter-replacement' => 'Voor een goede luchtkwaliteit is het belangrijk om regelmatig de filter te vervangen. Kijk in de gebruiksaanwijzing hoe vaak de filters vervangen moeten worden.',
            'closed' => 'Zorg dat de roosters in de woonkamer en slaapkamers altijd open staan. Schone lucht in huis is noodzakelijk voor je gezondheid.',
        ],

        'indication-for-costs' => [
            'title' => 'Indicatie voor kosten en baten voor kierdichting',
            'help'  => 'Hier kunt u zien wat de indicatieve kosten voor deze maatregel zijn.',
        ],
        'indication-for-costs-other' => [
            'title' => 'Indicatie voor kosten en baten overige maatregelen',
            'help'  => 'Hier kunt u zien wat de indicatieve kosten voor de overige maatregelen zijn.',
            'text' => 'Om te bepalen welke oplossing voor uw woning de beste is wordt geadviseerd om dit door een specialist te laten beoordelen.',
        ],
    ],
];