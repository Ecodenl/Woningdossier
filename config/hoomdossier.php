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
    'supported_locales' => [
        'nl',
//        'en',
    ],

    'cache' => [
        'prefix' => env('CACHE_PREFIX', 'hoomdossier_'),
        'times' => [
            'default' => 900, // ttl in seconds
        ],
    ],

    'services' => [
        'bag' => [
            'secret' => env('BAG_API_KEY', '')
        ],
    ],

    'webhooks' => [
        'discord' => env('DISCORD_WEBHOOK_URL')
    ],

    'media' => [
        'accepted_file_mimes' => env('MEDIA_FILE_MIMES', 'doc,dot,docx,dotx,docm,dotm,pdf,txt'),
        'accepted_image_mimes' => env('MEDIA_IMAGE_MIMES', 'jpg,jpeg,png'),
        'max_size' => env('MEDIA_MAX_SIZE', 16384), // KB
    ],
];
