<?php

namespace App\Deprecation;

use Illuminate\Support\Facades\Log;

/**
 * Just a little wrapper class to help with deprecation and reminders. It logs
 * some text, and makes sure we can grep on particular texts.
 *
 * @class DeprecationLogger
 */
class DeprecationLogger
{
    public static function remindMe(string $message): void
    {
        Log::warning('Note to self: '.$message);
    }

    public static function warning(string $message): void
    {
        Log::warning('DEPRECATION WARNING: '.$message);
    }

    public static function alert(string $message): void
    {
        Log::alert('DEPRECATION ALERT: '.$message);
    }

    public static function log(string $message): void
    {
        Log::debug("Deprecation: " . $message);
    }
}