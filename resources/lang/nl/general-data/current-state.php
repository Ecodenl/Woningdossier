<?php

return [
    'comment' => [
        'title' => 'Toelichting gebouwkenmerken',
        'help' =>  'Toelichting gebouwkenmerken',
    ],
    'step-intro' => [
        'title' => 'Uitlegtekst over de huidige staat van de woning en hoe deze pagina in te vullen. Uitlegtekst over de huidige staat van de woning en hoe deze pagina in te vullen Uitlegtekst over de huidige staat van de woning en hoe deze pagina in te vullen.',
    ],
    'service' => [
        'hybrid-heat-pump' => [
            'title' => '',
            'help' => ''
        ],
        'full-heat-pump' => [
            'title' => '',
            'help' => ''
        ],

        'sun-boiler' => [
            'title' => '',
            'help' => ''
        ],

        'hr-boiler' => [
            'title' => 'HR CV ketel',
            'help' => 'Hier kunt u aangeven of er in de huidige situatie een cv ketel aanwezig is en hoe oud deze ongeveer is.'
        ],

        'boiler' => [
            'title' => 'Type ketel',
            'help' => 'Hier kunt u aangeven welk type ketel u heeft. Als u het niet weet kies dan HR107 ketel.'
        ],

        'house-ventilation' => [
            'title' => 'Hoe wordt het huis geventileerd',
            'help' => '<p><strong>Natuurlijke ventilatie:</strong> De lucht in de woning wordt zonder behulp van ventilatoren ververst, bijvoorbeeld door ramen open te doen en/of ventilatieroosters. Een badkamer ventilator en de keukenafzuiging hoort daar niet bij.</p>
<p><strong>Mechanisch:</strong> De lucht wordt door een ventilator continu afgezogen, de lucht komt via roosters of open ramen naar binnen. De ventilatiebox zit vaak op zolder.</p>
<p><strong>Gebalanceerd:</strong> De lucht wordt mechanisch afgevoerd en mechanisch ingeblazen. Dit systeem is vaak in nieuwbouw woningen aanwezig.</p>
<p><strong>Decentraal mechanisch:</strong> De lucht wordt per kamer met een apart apparaat afgezogen en ingeblazen. De ventilatie-unit kan bijvoorbeeld geïntegreerd zijn in een radiator.</p>
<p><strong>Vraaggestuurd:</strong> Bij vraaggestuurde ventilatie gaat het altijd om een mechanisch systeem dat door luchtsensoren in de ruimtes gestuurd wordt. Bijvoorbeeld op basis van co2 of vochtgehalte.</p>'
        ],

        '' => [
            'title' => '',
            'help' => ''
        ],

    ],
    'element' => [
        'living-rooms-windows' => [
            'title' => 'Ramen in de leefruimtes',
            'help' => '<p>Als er meerdere soorten glas voorkomen, kies dan hier de soort met de grootste oppervlakte.</p><p><strong><span style="font-family: helvetica, arial, sans-serif;">Herkennen van de glassoort:</span></strong></p><p><span style="font-family: helvetica, arial, sans-serif;">In veel gevallen staat er in de spouw van dubbel glas een code geprint waar ook de glastype wordt genoemd, bijvoorbeeld HR of HR++. Soms staat er alleen een code in de spouw, is dit het geval dan kunt u aan de hand van de code bij de glasfabrikant navraag doen. De fabrikant kan u precies vertellen welk type glas er is geleverd.</span></p>'
        ],
        'sleeping-rooms-windows' => [
            'title' => 'Ramen in de slaapruimtes',
            'help' => '<p>Als er meerdere soorten glas voorkomen, kies dan hier de soort met de grootste oppervlakte.</p><p><strong><span style="font-family: helvetica, arial, sans-serif;">Herkennen van de glassoort:</span></strong></p><p><span style="font-family: helvetica, arial, sans-serif;">In veel gevallen staat er in de spouw van dubbel glas een code geprint waar ook de glastype wordt genoemd, bijvoorbeeld HR of HR++. Soms staat er alleen een code in de spouw, is dit het geval dan kunt u aan de hand van de code bij de glasfabrikant navraag doen. De fabrikant kan u precies vertellen welk type glas er is geleverd.</span></p>'
        ],
        'wall-insulation' => [
            'title' => 'Gevelisolatie',
            'help' => '<p>Denk aan de volgende isolatiemogelijkheden:</p>
<p>- Gevelisolatie tijdens de bouw, <br>- Spouwmuurisolatie, <br>- Isolerende voorzetwanden binnen, <br>- Buitengevelisolatie.</p>
<p><strong>Geen isolatie</strong> = gevels met 2 cm isolatie of minder</p>
<p><strong>Matige isolatie</strong> = gevels met een Rc-waarde van minder dan 2,58 m<sup>2 </sup>K/W</p>
<p><strong>Goede isolatie</strong> = gevels met een Rc-waarde tussen 2,58 m<sup>2 </sup>K/W en 3,99 m<sup>2 </sup>K/W</p>
<p><strong>Zeer goede isolatie</strong> = gevels met een Rc-waarde van 4,00 m<sup>2 </sup>K/W of hoger</p>
<p>Als er meerdere gevels met verschillende isolatiediktes aanwezig zijn, kies dan voor de isolatiedikte die het meeste voorkomt of maak er een gemiddelde van.</p>'
        ],
        'floor-insulation' => [
            'title' => 'Vloerisolatie',
            'help' => '<p>Denk aan de volgende isolatiemogelijkheden:</p>
<p>- Tonzonisolatie, <br>- Isolatie tussen de balken, <br>- PUR isolatie, <br>- Isolatie op de bodem van de kruipruimte.</p>
<p><strong>Geen isolatie</strong> = vloeren met 2 cm isolatie of minder</p>
<p><strong>Matige isolatie</strong> = vloeren met een Rc-waarde van minder dan 2,37 m<sup>2 </sup>K/W</p>
<p><strong>Goede isolatie</strong> = vloeren met een Rc-waarde tussen 2,37 m<sup>2 </sup>K/W en 3,49 m<sup>2 </sup>K/W</p>
<p><strong>Zeer goede isolatie</strong> = vloeren met een Rc-waarde van 3,50 m<sup>2 </sup>K/W of hoger</p>
<p>Als er meerdere vloeren met verschillende isolatiediktes aanwezig zijn, kies dan voor de isolatiedikte die het meeste voorkomt of maak er een gemiddelde van.</p>'
        ],
        'roof-insulation' => [
            'title' => 'Dakisolatie',
            'help' => '<p>Denk aan de volgende isolatiemogelijkheden:</p>
<p>- Isolatie van binnenuit achter gipsplaten, <br>- Isolatie onder de dakpannen, <br>- Isolatie onder de dakbedekking.</p>
<p><strong>Geen isolatie</strong> = daken met 2 cm isolatie of minder</p>
<p><strong>Matige isolatie</strong> = daken met een Rc-waarde van minder dan 2,44 m<sup>2 </sup>K/W</p>
<p><strong>Goede isolatie</strong> = daken met een Rc-waarde tussen 2,44 m<sup>2 </sup>K/W en 3,99 m<sup>2 </sup>K/W</p>
<p><strong>Zeer goede isolatie</strong> = daken met een Rc-waarde van 4,00 m<sup>2 </sup>K/W of hoger</p>
<p>Als er meerdere daken met verschillende isolatiediktes aanwezig zijn, kies dan voor de isolatiedikte die het meeste voorkomt of maak er een gemiddelde van.</p>'
        ],
        'crack-sealing' => [
            'title' => 'Zijn de ramen en deuren voorzien van kierdichting?',
            'help' => 'Kierdichting'
        ],
    ],
];