<?php

return [
    'index' => [
        'comment' => [
            'title' => 'Toelichting gebouwkenmerken',
            'help' => 'Toelichting gebouwkenmerken',
        ],
        'building-type' => [
            'title' => 'Wat is het woningtype?',
            'help' => 'Het woningtype wordt gebruikt als basis voor een aantal berekeningen en wordt daarom hier apart gevraagd. Een omschrijving van de verschillende woningtypen is te vinden op de website van RVO: <a href="https://energiebesparingsverkenner.rvo.nl/Help/Uitleg#woningomschrijvingen">https://energiebesparingsverkenner.rvo.nl/Help/Uitleg#woningomschrijvingen</a>'
        ],
        'build-year' => [
            'title' => 'Wat is het bouwjaar?',
            'help' => 'Het bouwjaar wordt automatisch opgezocht in het Kadaster. Als het bouwjaar onjuist wordt weergegeven kunt u deze handmatig aanpassen.'
        ],
        'surface' => [
            'title' => 'Wat is de gebruiksoppervlakte van de woning?',
            'help' => '<p>De gebruiksoppervlakte wordt automatisch opgezocht in het Kadaster. Als oppervlakte onjuist wordt weergegeven kunt u deze handmatig aanpassen.</p><p><span class="kop"><span class="subtitel"><strong>Wat is de gebruiksoppervlakte? </strong></span></span><br><br>De gebruiksoppervlakte wordt bepaald volgens NEN 2580. De gebruiksoppervlakte, ook wel afgekort met “GO”, is de bruikbare vloeroppervlakte, geschikt voor het beoogde gebruik. Dit is in feite het totale vloeroppervlak tussen de omsluitende wanden van de gebruiksfunctie minus de vaste obstakels van enige omvang:</p><ul><li>dragende binnen wanden;</li><li>schalmgat, vide, liftschacht als oppervlakte &gt; 4 m<sup>2 </sup></li><li>vloeroppervlakte met een vrije hoogte kleiner dan 1,5 meter (maar wel de vloer onder een trap of hellingbaan meetellen)</li><li>een trapgat, liftschacht of vide, indien de oppervlakte daarvan groter is dan 4 m<sup>2 </sup></li><li>een vrijstaande bouwconstructie, niet zijnde een trap, en een leidingschacht, indien de horizontale doorsnede daarvan groter is dan 0,5 m<sup>2 </sup></li></ul><p>Bij de bepaling van de grenslijn, dient een incidentele nis of uitsparing en een incidenteel uitspringend bouwdeel te worden genegeerd, voor zover het grondvlak daarvan kleiner is dan 0,5 m<sup>2 </sup>.</p>'
        ],
        'building-layers' => [
            'title' => 'Hoeveel bouwlagen heeft het huis?',
            'help' => 'De begane grond van een woning is de eerste bouwlaag, eerste verdieping is de tweede bouwlaag enz. Meegeteld worden hierbij alleen bewoonde bouwlagen, kelders en onverwarmde zolders horen hier vaak niet bij. Tel bij appartementen en dergelijke alleen de eigen bouwlagen mee.'
        ],
        'roof-type' => [
            'title' => 'Type dak',
            'help' => 'Bedoeld is het hoofddak. Het dak van een aanbouw of van een dakkapel telt niet mee. Heeft het hoofdak zowel platte als ook hellende delen dan telt het dak met de grootste oppervlakte. Een appartement op een tussenlaag of een benedenwoning heeft geen dak.'
        ],
        'energy-label' => [
            'title' => 'Wat is het huidige energielabel?',
            'help' => 'Op te zoeken via de energielabel database: https://www.zoekuwenergielabel.nl/ Met dit veld wordt niet gerekend, het dient alleen om de huidige status van het energielabel vast te leggen'
        ],
        'monument' => [
            'title' => 'Is het een monument?',
            'help' => 'Op te zoeken via de website van uw gemeente (voor gemeentelijke monumenten) of op https://cultureelerfgoed.nl/monumentenregister (voor rijksmonumenten) Met dit veld wordt niet gerekend, voor uitvoerende partijen is het echter belangrijke informatie, die op deze manier altijd paraat is.'
        ],
        'example-building' => [
            'apply-are-you-sure' => [
                'title' => 'Weet u zeker dat u deze voorbeeldwoning wilt toepassen'
            ],
            'title' => 'Er zijn voor uw situatie specifieke voorbeeldwoningen aanwezig: Kies hier de best passende woning.',
            'no-match' => [
                'title' => 'Er is geen passende voorbeeldwoning',
            ],
            'help' => 'Uw coöperatie heeft voor uw woningtype een (aantal) specifieke voorbeeldwoning(en) ter beschikking gesteld. Kies hier de best passende optie. Mocht er geen passende woning tussen staan, kies dan voor de optie "Er is geen passende voorbeeldwoning". Het Hoomdossier rekent dan verder met de algemene voorbeeldwoningen.'
        ],
    ],
];