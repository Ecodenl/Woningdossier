<?php

namespace App\Helpers;

use Closure;

class Wrapper
{
    public static function wrapCall(Closure $closure, ?Closure $uponFailed = null)
    {
        $results = null;
        try {
            $results = $closure();
        } catch (\Throwable $exception) {
            report($exception);

            if (! is_null($uponFailed)) {
                $uponFailed($results, $exception);
            }
        }

        return $results;
    }
}