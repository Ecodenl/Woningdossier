<?php

namespace App\Helpers;

class Queue
{
    public const DEFAULT = 'default';
    public const ASYNC = 'async';
    public const REGULATIONS = 'regulations';

    public static function getQueueNames(): array
    {
        return [
            self::DEFAULT,
            self::ASYNC,
            self::REGULATIONS,
        ];
    }
}