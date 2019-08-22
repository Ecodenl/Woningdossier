<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Domain
    |--------------------------------------------------------------------------
    |
    | Basically the same as APP_URL, but without the protocol. It
    |
    */
    'domain' => env('APP_DOMAIN', 'hoomdossier.nl'),

    /*
    |--------------------------------------------------------------------------
    | Supported locales
    |--------------------------------------------------------------------------
    |
    | This array is used for rendering the language options as well as to
    | supply the UserLanguage middleware options for the getPreferredLanguage
    | method.
    |
    */
    'supported_locales' => ['nl'],

    'cache' => [
        'times' => [
            'default' => 15, // minutes (watch it: this will change with the latest Laravel versions!)
        ],
    ],
];
