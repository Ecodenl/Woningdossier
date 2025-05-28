<?php

use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    'channels' => [
        'api' => [
            'driver' => 'single',
            'path' => storage_path('logs/api.log'),
            'days' => 14
        ],
        'calculations' => [
            'driver' => 'single',
            'path' => storage_path('logs/calculations.log'),
            'days' => 14
        ],
    ],
];
