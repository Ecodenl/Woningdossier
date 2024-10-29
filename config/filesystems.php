<?php

return [

    'disks' => [
        'downloads' => [
            'driver' => 'local',
            'root' => storage_path('app/downloads'),
            'visibility' => 'private',
        ],

        'uploads' => [
            'driver' => 'local',
            'root' => storage_path('app/public/uploads'),
            'url' => env('APP_URL') . '/storage/uploads',
            'visibility' => 'private',
        ],

        'exports' => [
            'driver' => 'local',
            'root' => storage_path('app/exports'),
            'visibility' => 'private',
        ],
    ],

];
