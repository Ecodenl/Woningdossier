<?php

return [
    'disks' => [
        'downloads' => [
            'driver' => 'local',
            'root' => storage_path('app/private/downloads'),
            'visibility' => 'private',
            'serve' => true,
            'throw' => false,
        ],

        'uploads' => [
            'driver' => 'local',
            'root' => storage_path('app/private/uploads'),
            //'url' => env('APP_URL') . '/storage/uploads',
            'serve' => true,
            'throw' => false,
            'visibility' => 'private',
        ],

        'exports' => [
            'driver' => 'local',
            'root' => storage_path('app/private/exports'),
            'visibility' => 'private',
            'serve' => true,
            'throw' => false,
        ],
    ],
];
