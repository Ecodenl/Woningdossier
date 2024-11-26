<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

/**
 * Just a little wrapper class to help with deprecation and reminders. It logs
 * some text, and makes sure we can grep on particular texts.
 *
 * @class Deprecation
 */
class Deprecation
{
    public static function remindMe($text)
    {
        Log::warning('Note to self: '.$text);
    }

    public static function warning($text)
    {
        Log::warning('DEPRECATION WARNING: '.$text);
    }

    public static function alert($text)
    {
        Log::alert('DEPRECATION ALERT: '.$text);
    }
}
