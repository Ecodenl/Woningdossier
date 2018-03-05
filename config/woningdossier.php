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
	'domain' => env('APP_DOMAIN', 'hoom-dossier.nl'),

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
	'supported_locales' => ['nl', 'en'],

];