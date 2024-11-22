<?php

return [

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'accounts',
        ],
    ],

    'providers' => [
        'accounts' => [
            'driver' => 'eloquent',
            'model' => App\Models\Account::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'accounts',
            'table' => 'password_reset_tokens',
            'expire' => env('AUTH_PASSWORD_RESET_EXPIRE', 43200),
            'throttle' => 60,
        ],
    ],

];
