<?php

namespace App\Deprecation;

use Illuminate\Support\Facades\Log;

class DeprecationLogger
{
    public static function log(string $message): void
    {
        Log::debug("Deprecation: " . $message);
    }
}