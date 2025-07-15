<?php

namespace App\Helpers;

class Queue
{
    // TODO: Make enum

    public const string APP = 'app';
    public const string APP_HIGH = 'app_high';
    public const string APP_EXTERNAL = 'app_external';
    public const string LOGS = 'logs';
    public const string EXPORTS = 'exports';
    // ideally we would want to remove this, however this is used by laravel as a default
    // so if we forget to set the queue or a laravel internal changes
    // the job may not me picked up.
    public const string DEFAULT = 'default';

    public static function getQueueNames(): array
    {
        return [
            self::APP,
            self::APP_HIGH,
            self::APP_EXTERNAL,
            self::LOGS,
            self::EXPORTS,
            self::DEFAULT,
        ];
    }
}
