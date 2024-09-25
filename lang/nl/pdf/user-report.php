<?php

use App\Services\UserActionPlanAdviceService;

return [
    'defaults' => [
        'page' => 'Pagina',
    ],
    'alerts' => [
        'title' => 'Notificaties',
        'text' => 'Let op, er zijn aandachtspunten bij de gekozen maatregelen, kijk in de bijlage voor details.',
    ],
    'pages' => [
        'front-page' => [
            'date' => 'Datum:',
            'connected-coaches' => 'Energiecoach:|Energiecoaches:',
            'title' => 'Stappenplan voor het verduurzamen<br>van jouw woning',
            'text' => 'U hebt het Hoomdossier voor uw woning ingevuld. Hieronder ziet u een samenvatting van de ingevoerde gegevens, de resultaten van de berekeningen en de stappen die u kunt ondernemen om uw woning duurzamer te maken.',
        ],
        'action-plan' => [
            'title' => 'Dit is jouw Woonplan',
            'text' => 'Je hebt het Hoomdossier voor je woning ingevuld. Hieronder zie je een samenvatting van het Woonplan zoals je die hebt ingericht.',
            'usage' => [
                'current' => 'Dit is je huidig energiegebruik:',
                'kengetallen' => 'In jouw Woonplan hebben we met de volgende energieprijzen gerekend:',
            ],
            'categories' => [
                UserActionPlanAdviceService::CATEGORY_COMPLETE => 'De volgende maatregelen heb je al gedaan:',
                UserActionPlanAdviceService::CATEGORY_TO_DO => 'De volgende maatregelen kun je het beste nu aanpakken:',
                UserActionPlanAdviceService::CATEGORY_LATER => 'De volgende maatregelen kan je ook later uitvoeren: ',
            ],
            'advices' => [
                'measure' => 'Maatregel',
                'cost-indication' => 'Kostenindicatie [ € ]',
                'savings' => 'Besparing [ € / jaar ]',
            ],
            'comment' => 'Toelichting op het Woonplan',
        ],
        'info-page' => [
            'subsidy' => [
                'title' => 'Subsidie en financiering',
                'available-for' => 'Je kunt subsidie aanvragen voor de volgende maatregelen:',
                'none-available' => 'Voor de gekozen maatregelen zijn geen subsidies gevonden.',
                'text' => 'Meer informatie over de regelingen kun je in de online-versie van jouw Hoomdossier of op de site <a href="https://www.verbeterjehuis.nl/energiesubsidiewijzer" target="_blank" rel="nofollow">https://www.verbeterjehuis.nl/energiesubsidiewijzer</a> vinden.'
            ],
            'calculations-are-indicative' => [
                'title' => 'Berekeningen zijn indicatief',
                'text' => 'Met het Hoomdossier proberen we een zo goed mogelijke inschatting te maken van de kansen en mogelijkheden om jouw huis te verduurzamen. Het Hoomdossier maakt gebruik van formules en vergelijkingen die een benadering zijn van de werkelijkheid. Hoewel het Hoomdossier inzicht geeft in de mogelijke impact van energiebesparende maatregelen, kan het een persoonlijk advies op maat niet vervangen. De feitelijke kosten en opbrengsten van een investering bepaal je vervolgens op basis van offertes. Je kunt het wel gebruiken als basis voor een keukentafelgesprek of een professioneel advies. Er kan geen garantie worden ontleend aan de resultaten van het Hoomdossier ten aanzien van de daadwerkelijke energieprestaties, berekend energiegebruik of besparingen. De essentie van werken met Hoomdossier is het krijgen van inzicht in de kosten en baten van energiebesparende maatregelen.',
            ],
            'more-info' => [
                'title' => 'Meer informatie',
                'text' => 'Het Hoomdossier wordt aangeboden door :cooperation.',
                'website' => 'Meer informatie en onze volledige contactgegevens kunt u vinden op :url.'
            ],
        ],
        'simple-scan-answers' => [
            'title' => 'Samenvatting woongegevens',
            'text' => 'De volgende gegevens heb je ingevuld in het Hoomdossier:',
        ],
        'expert-scan-answers' => [
            'title' => 'Jouw uitgebreide Woonplan',
            'text' => 'Op de volgende bladzijden zie je voor de maatregelen die in jouw Woonplan staan een samenvatting van de ingevoerde gegevens en de gedetailleerde uitkomst van de berekeningen. Meer informatie over de maatregelen kun je ook vinden op de website van Milieucentraal <a href="www.milieucentraal.nl" target="_blank" rel="nofollow">www.milieucentraal.nl</a>.',
            'action-plan' => 'In deze bijlage vind je de gegevens van de volgende maatregelen',
        ],
        'small-measures' => [
            'title' => 'Kleine en eigen maatregelen',
            'text' => 'Hier vind je uitleg over de kleine maatregelen, en over eventuele eigen aangemaakte maatregelen, of gekozen maatregelen van de cooperatie.',
        ],
        'coach-help' => [
            'title' => 'De coach heeft voor deze maatregelen',
        ],
    ],
    'step-description' => [
        'ventilation' => 'Voor uw gezondheid is een goede ventilatie met voldoende schone lucht noodzakelijk. Bij oudere slecht geïsoleerde huizen gaat luchtverversing vaak vanzelf via naden en kieren. Maar naarmate huizen beter geïsoleerd worden gebeurt dat steeds minder en is andere ventilatie nodig. Ventilatie zorgt voor minder vuile lucht in huis; helemaal te voorkomen is vervuiling niet. Ventilatie kost wat energie, maar het is geen verspilling: het is hard nodig voor uw gezondheid.',
        'wall-insulation' => 'Huizen met geïsoleerde gevels hebben lagere energielasten en een hoger comfortniveau doordat de muren minder koude afstralen. Bovendien vermindert het risico op condens en schimmelvorming op de muren.',
        'insulated-glazing' => 'Goed isolerende beglazing, HR++ of Triple, geeft een hoger comfort. U voelt minder koude van het raam afstralen en koudeval die door trekt als tocht over de vloer neemt af. Vervangt u enkel glas, dan komt er geen condens meer op de ruiten in de winter.',
        'floor-insulation' => 'Het isoleren van de begane grond vloer is vaak eenvoudig te realiseren en kan voor u veel comfort opleveren. De temperatuur in de woonkamer is na isolatie van de vloer gelijkmatiger verdeeld en er komt vanuit de kruipruimte nauwelijks nog vocht uw huis binnen. En u bespaart energie door het isoleren van de vloer.',
        'roof-insulation' => 'Warme lucht stijgt en gaat naar de bovenste verdieping. Bij niet geïsoleerde daken gaat deze warmte door het dak zelf en door naden en kieren verloren. Het isoleren van het dak zorgt ervoor dat de warmte in de winter binnen blijft en niet meer tocht bij de aansluitingen van het dak. In de zomer blijft de warmte door de isolatie juist buiten zorgt voor een koelere werk- of slaapplaats. Een gevoel van behaaglijkheid en comfort door de gehele woning is een groot voordeel van dakisolatie.',
        'high-efficiency-boiler' => 'Is uw cv-ketel ouder dan 15 jaar, dan is het tijd het om aan een nieuwe ketel te denken. De kans op storingen stijgt, en bovendien kan er het nodige aan uw situatie zijn veranderd. Is uw huishouden groter of kleiner geworden? Heeft u andere leefgewoontes? Nieuwe ketels zijn daarnaast beter in te stellen, waardoor ze ook nog eens zuiniger zijn.',
        'solar-panels' => 'Steeds meer mensen laten zonnepanelen monteren. Dat is wel zo slim. Gemiddeld verbruikt een gezin in Nederland zo’n 3500 kWh aan stroom per jaar. Met zonnepanelen kunt u deze energie zelf duurzaam opwekken. Dat scheelt op uw energierekening én is beter voor het milieu.',
        'heater' => 'Voor huishoudens waar veel wordt gedoucht, kan de zonneboiler een uitkomst zijn. Met een zonneboiler verwarmt u water met de energie van de zon. U kunt zo bijna de helft van uw energiegebruik voor warm water besparen. Ideaal voor gezinnen met kinderen.',
        'heat-pump' => 'Een warmtepomp zorgt op een milieuvriendelijke manier voor verwarming van je huis en warm water in de douche en keuken. Het is een duurzaam alternatief voor je cv-ketel op gas. Bovendien kun je bij aankoop subsidie krijgen en gaat je energierekening omlaag. Met een volledige warmtepomp kun je je huis aardgasvrij maken, met een hybride warmtepomp breng je het gasverbruik op korte termijn fors omlaag.',
        'heating' => 'Verwarming is een grote energieverbruiker in huis. Ongeveer de helft van je energierekening bestaat uit kosten voor verwarming, en 15 procent voor warm water. Op een energiezuinige en duurzame manier je huis en water verwarmen levert dus veel op, voor je portemonnee en het klimaat. De overstap van aardgas naar duurzame warmte wordt ook wel de energietransitie genoemd.' . PHP_EOL . 'Hieronder zie je de maatregelen die je voor duurzame warmte in je woning wilt toevoegen.',
    ],
];
