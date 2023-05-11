<?php

namespace App\Helpers;

class Queue
{
    public const APP = 'app';
    public const APP_HIGH = 'app_high';
    public const APP_EXTERNAL = 'app_external';
    public const LOGS = 'logs';
    public const EXPORTS = 'exports';

    public static function getQueueNames(): array
    {
        return [
            self::APP,
            self::APP_HIGH,
            self::APP_EXTERNAL,
            self::LOGS,
            self::EXPORTS,
        ];
    }
}