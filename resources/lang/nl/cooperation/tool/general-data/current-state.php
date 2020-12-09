<?php

return [
    'index' => [
        'comment' => [
            'element' => [
                'title' => 'Toelichting huidige staat isolatie',
                'help' => '<p>Hier kunnen aanvullende opmerkingen worden gemaakt over de huidige staat van de isolatie van de woning.</p>',
            ],
            'service' => [
                'title' => 'Toelichting huidige staat installaties',
                'help' => '<p>Hier kunnen aanvullende opmerkingen worden gemaakt over de huidige staat van de installaties in de woning.</p>',
            ],
        ],
        'step-intro' => [
            'title' => 'In deze stap kunt u per onderwerp aangegeven welke energiebesparingsmaatregelen er al zijn gerealiseerd. Deze informatie wordt gebruikt voor het berekenen van kosten en besparingen van de nog te realiseren maatregelen.',
        ],
        'installed-power' => [
            'title' => 'Geinstalleerd vermogen (totaal)',
            'help' => '<p>Hier kunt u het totale Wp vermogen van de huidige aanwezige zonnepanelen aangeven.</p>',
        ],
        'building-heating-applications' => [
            'title' => 'Hoe wordt de woning nu verwarmd?',
        ],
        'service' => [
            'heat-pump' => [
                'title' => 'Warmtepomp',
                'help' => '<p>Hier kunt u aangeven of er in de huidige situatie een warmtepomp aanwezig is.</p>',
            ],
            'sun-boiler' => [
                'title' => 'Zonneboiler',
                'help' => '<p>Hier kunt u aangeven of u in de huidige situatie een zonneboiler hebt en waarvoor u de warmte gebruikt.</p>',
            ],
            'hr-boiler' => [
                'title' => 'HR CV ketel',
                'help' => '<p>Hier kunt u aangeven of er in de huidige situatie een cv ketel aanwezig is en hoe oud deze ongeveer is.</p>',
            ],
            'boiler' => [
                'title' => 'Type ketel',
                'help' => '<p>Hier kunt u aangeven welk type ketel u heeft. Deze informatie is vaak te vinden op de ketel zelf of in de bijgeleverde documentatie. Als u het niet weet kies dan HR107 ketel.&nbsp;</p>',
            ],
            'total-sun-panels' => [
                'title' => 'Hoeveel zonnepanelen zijn er aanwezig?',
                'help' => '<p>Voer hier het aantal zonnepanelen in dat in de huidige situatie ge&iuml;nstalleerd is. Als u geen panelen hebt vul dan 0 in of laat het veld leeg.</p>',
                'year' => [
                    'title' => 'Indien aanwezig, wanneer geplaatst?',
                    'help' => '<p>Hier kunt u aangeven in welk jaar de panelen zijn geplaatst. Als u het niet weet kunt u het veld leeglaten.</p>',
                ],
            ],
            'house-ventilation' => [
                'title' => 'Hoe wordt het huis geventileerd?',
                'help' => '<p><strong>Natuurlijke ventilatie:</strong> De lucht in de woning wordt zonder behulp van ventilatoren ververst, bijvoorbeeld door ramen open te doen en/of ventilatieroosters. Een badkamer ventilator en de keukenafzuiging hoort daar niet bij.</p>
<p><strong>Mechanisch:</strong> De lucht wordt door een ventilator continu afgezogen, de lucht komt via roosters of open ramen naar binnen. De ventilatiebox zit vaak op zolder.</p>
<p><strong>Gebalanceerd:</strong> De lucht wordt mechanisch afgevoerd en mechanisch ingeblazen. Dit systeem is vaak in nieuwbouw woningen aanwezig.</p>
<p><strong>Decentraal mechanisch:</strong> De lucht wordt per kamer met een apart apparaat afgezogen en ingeblazen. De ventilatie-unit kan bijvoorbeeld ge&iuml;ntegreerd zijn in een radiator.</p>
<p><strong>Vraaggestuurd:</strong> Bij vraaggestuurde ventilatie gaat het altijd om een mechanisch systeem dat door luchtsensoren in de ruimtes gestuurd wordt. Bijvoorbeeld op basis van co2 of vochtgehalte.</p>',
                'demand-driven' => [
                    'title' => 'Vraaggestuurde regeling',
                    'help' => '<p>helptext voor vraaggestuurde regeling</p>',
                ],
                'heat-recovery' => [
                    'title' => 'Met warmte terugwinning',
                    'help' => '<p>helptext Met warmte terugwinning</p>',
                ],
            ],
        ],
        'element' => [
            'living-rooms-windows' => [
                'title' => 'Ramen in de leefruimtes',
                'help' => '<p>Als er meerdere soorten glas voorkomen, kies dan hier de soort met de grootste oppervlakte.</p>
<p><strong><span style="font-family: helvetica, arial, sans-serif;">Herkennen van de glassoort:</span></strong></p>
<p><span style="font-family: helvetica, arial, sans-serif;">In veel gevallen staat er in de spouw van dubbel glas een code geprint waar ook de glastype wordt genoemd, bijvoorbeeld HR of HR++. Soms staat er alleen een code in de spouw, is dit het geval dan kunt u aan de hand van de code bij de glasfabrikant navraag doen. De fabrikant kan u precies vertellen welk type glas er is geleverd.</span></p>',
            ],
            'sleeping-rooms-windows' => [
                'title' => 'Ramen in de slaapruimtes',
                'help' => '<p>Als er meerdere soorten glas voorkomen, kies dan hier de soort met de grootste oppervlakte.</p>
<p><strong><span style="font-family: helvetica, arial, sans-serif;">Herkennen van de glassoort:</span></strong></p>
<p><span style="font-family: helvetica, arial, sans-serif;">In veel gevallen staat er in de spouw van dubbel glas een code geprint waar ook de glastype wordt genoemd, bijvoorbeeld HR of HR++. Soms staat er alleen een code in de spouw, is dit het geval dan kunt u aan de hand van de code bij de glasfabrikant navraag doen. De fabrikant kan u precies vertellen welk type glas er is geleverd.</span></p>',
            ],
            'wall-insulation' => [
                'title' => 'Gevelisolatie',
                'help' => '<p>Denk aan de volgende isolatiemogelijkheden:</p>
<p>- Gevelisolatie tijdens de bouw, <br />- Spouwmuurisolatie, <br />- Isolerende voorzetwanden binnen, <br />- Buitengevelisolatie.</p>
<p><strong>Geen isolatie</strong> = gevels met 2 cm isolatie of minder</p>
<p><strong>Matige isolatie</strong> = gevels met een Rc-waarde van minder dan 2,58 m<sup>2 </sup>K/W</p>
<p><strong>Goede isolatie</strong> = gevels met een Rc-waarde tussen 2,58 m<sup>2 </sup>K/W en 3,99 m<sup>2 </sup>K/W</p>
<p><strong>Zeer goede isolatie</strong> = gevels met een Rc-waarde van 4,00 m<sup>2 </sup>K/W of hoger</p>
<p>Als er meerdere gevels met verschillende isolatiediktes aanwezig zijn, kies dan voor de isolatiedikte die het meeste voorkomt of maak er een gemiddelde van.</p>',
            ],
            'floor-insulation' => [
                'title' => 'Vloerisolatie',
                'help' => '<p>Denk aan de volgende isolatiemogelijkheden:</p>
<p>- Tonzonisolatie, <br />- Isolatie tussen de balken, <br />- PUR isolatie, <br />- Isolatie op de bodem van de kruipruimte.</p>
<p><strong>Geen isolatie</strong> = vloeren met 2 cm isolatie of minder</p>
<p><strong>Matige isolatie</strong> = vloeren met een Rc-waarde van minder dan 2,37 m<sup>2 </sup>K/W</p>
<p><strong>Goede isolatie</strong> = vloeren met een Rc-waarde tussen 2,37 m<sup>2 </sup>K/W en 3,49 m<sup>2 </sup>K/W</p>
<p><strong>Zeer goede isolatie</strong> = vloeren met een Rc-waarde van 3,50 m<sup>2 </sup>K/W of hoger</p>
<p>Als er meerdere vloeren met verschillende isolatiediktes aanwezig zijn, kies dan voor de isolatiedikte die het meeste voorkomt of maak er een gemiddelde van.</p>',
            ],
            'roof-insulation' => [
                'title' => 'Dakisolatie',
                'help' => '<p>Denk aan de volgende isolatiemogelijkheden:</p>
<p>- Isolatie van binnenuit achter gipsplaten, <br />- Isolatie onder de dakpannen, <br />- Isolatie onder de dakbedekking.</p>
<p><strong>Geen isolatie</strong> = daken met 2 cm isolatie of minder</p>
<p><strong>Matige isolatie</strong> = daken met een Rc-waarde van minder dan 2,44 m<sup>2 </sup>K/W</p>
<p><strong>Goede isolatie</strong> = daken met een Rc-waarde tussen 2,44 m<sup>2 </sup>K/W en 3,99 m<sup>2 </sup>K/W</p>
<p><strong>Zeer goede isolatie</strong> = daken met een Rc-waarde van 4,00 m<sup>2 </sup>K/W of hoger</p>
<p>Als er meerdere daken met verschillende isolatiediktes aanwezig zijn, kies dan voor de isolatiedikte die het meeste voorkomt of maak er een gemiddelde van.</p>',
            ],
            'crack-sealing' => [
                'title' => 'Kierdichting aanwezig?',
                'help' => '<p>Geef hier aan of er kierdichting voor de draaiende delen van ramen en deuren aanwezig is. Het gaat om de situatie voor de gehele woning. Als er specifieke situaties zijn kan dat in het opmerkingenveld verder worden toegelicht.</p>',
            ],
        ],
    ],
];
