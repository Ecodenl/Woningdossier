<?php

return [
    /**
     * The table where the translations are stored
     */
    'table' => 'language_lines',

    /**
     * The column where the translations group should be stored
     */
    'group' => 'group',

    /**
     * The column where the translation key should be stored
     */
    'key' => 'key',

    /**
     * The column where the translation text itself should be stored
     */
    'translations' => 'text',

    /**
     * Array of functions which are used to get translations
     */
    'trans_functions' => [
        'trans',
        'trans_choice',
        'Lang::get',
        'Lang::choice',
        'Lang::trans',
        'Lang::transChoice',
        '@lang',
        '@choice',
        '__',
        '$trans.get',
    ],
];
