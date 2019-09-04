<?php

return [
    'users' => [
        'index'  => [
            'header' => 'Zoeken naar gebruikers',
            'form' => [
                'user' => [
                    'title' => 'Filteren op gebruiker',
                    'first-name' => 'Voornaam',
                    'last-name' => 'Achternaam'
                ],
                'account' => [
                    'email' => 'Email'
                ],
                'building' => [
                    'title' => 'Filteren op adres',
                    'street' => 'Straat',
                    'number' => 'Huisnummer',
                    'city' => 'Stad',
                    'postal-code' => 'Postcode',
                ],

                'submit' => 'Zoeken',
            ],
        ],
        'show'  => [
            'header' => 'Zoeken naar gebruikers',

            'table' => [
                'columns' => [
                    'date'                => 'Datum',
                    'name'                => 'Naam',
                    'street-house-number' => 'Straat en huisnummer',
                    'zip-code'            => 'Postcode',
                    'city'                => 'Stad',
                    'status'              => 'Status',
                    'no-known-created-at' => 'Niet bekend'
                ],
            ],
        ],
        'edit'   => [
            'header'       => 'Alle vragen die vertaalbaar zijn op de stap :step_name',
            'sub-question' => 'Laat sub-vragen zien',
            'question'     => 'Vraag in taal: :locale',
            'help'         => 'Helptext in taal: :locale',
            'search'       => [
                'placeholder' => 'Zoek naar een vraag..',
            ],
            'save'         => 'Wijzigingen voor de vragen, sub-vragen en helpteksten opslaan.',
            'close-modal'  => 'Sluit venster.',
        ],
        'update' => [
            'success' => 'Vertalingen zijn bijgewerkt.',
        ],
    ],
];