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

    'queue' => [
        'warning_size' => env('QUEUE_WARNING_SIZE', 1000),
    ],

    'cache' => [
        'prefix' => env('CACHE_PREFIX', 'hoomdossier_'),
        'times' => [
            'default' => 900, // ttl in seconds
        ],
    ],

    'services' => [
        'ep_online' => [
            'secret' => env('EP_ONLINE_API_KEY', ''),
        ],
        'bag' => [
            'secret' => env('BAG_API_KEY', ''),
        ],
        'econobis' => [
            'enabled' => env('ECONOBIS_ENABLED', false),
            'wildcard' => env('ECONOBIS_WILDCARD', 'test'),
            'debug' => env('ECONOBIS_DEBUG', false),
            'warn' => env('ECONOBIS_WARN', false),
            'api-key' => env('ECONOBIS_KEY', ''),
            // after how many minutes may the woonplan be sent to econobis?
            'send_woonplan_after_change' => env('ECONOBIS_SEND_WOONPLAN_AFTER_CHANGE', 30),
            'interval' => [
                \App\Jobs\Econobis\Out\SendPdfReportToEconobis::class => env('ECONOBIS_INTERVAL_PDF_REPORT', 30),
            ],
        ],
        'enable_logging' => env('SERVICES_ENABLE_LOGGING', false),
        'enable_calculation_logging' => env('SERVICES_ENABLE_CALCULATION_LOGGING', false),
    ],

    'webhooks' => [
        'discord' => env('DISCORD_WEBHOOK_URL')
    ],

    'media' => [
        'accepted_file_mimes' => env('MEDIA_FILE_MIMES', 'doc,dot,docx,dotx,docm,dotm,pdf,txt'),
        'accepted_image_mimes' => env('MEDIA_IMAGE_MIMES', 'jpg,jpeg,png'),
        'max_size' => env('MEDIA_MAX_SIZE', 16384), // KB

        'custom' => [
            \App\Helpers\MediaHelper::PDF_BACKGROUND => [
                'max_size' => env('PDF_BACKGROUND_MEDIA_MAX_SIZE', 1000), // KB
            ],
            \App\Helpers\MediaHelper::BUILDING_IMAGE => [
                'max_size' => env('BUILDING_IMAGE_MEDIA_MAX_SIZE', 1000), // KB
            ],
        ],
    ],

    'contact' => [
        'email' => [
            // Email addresses of the admins, those admins should be notified in case something happens.
            'admin' => env('ADMIN_MAIL_ADDRESS', ''),
            // Email addresses that should be allowed through the email filter if the app isn't in production.
            'whitelist' => env('HOOM_CONTACT_EMAIL_WHITELIST', ''),
            'whitelist_enabled' => env('HOOM_CONTACT_EMAIL_WHITELIST_ENABLED', true),
        ],
    ],

    'security' => [
        // Set to true to set CSP in report-only mode (for testing)
        'csp_report_only' => env('CSP_REPORT_ONLY', false),
        // Only applicable for production + HTTPS
        'hsts' => env('HSTS_VALUE', 'max-age=31536000; includeSubDomains; preload'),
        'referrer_policy' => env('REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        'permissions_policy' => env('PERMISSIONS_POLICY', "accelerometer=(), camera=(), microphone=(), geolocation=(self), fullscreen=(self), usb=(), payment=()"),
        // Applicable for production: Allow inline styles if really required (e.g. by a library).
        'allow_unsafe_inline_styles' => env('ALLOW_UNSAFE_INLINE_STYLES', false),
        // Dev only. Most of the time you will use the default or you have set this in the .env as the VITE_PORT is also changed in the docker-compose.
        'vite_host' => env('VITE_HOST', 'http://localhost:5173'),
    ],
];
