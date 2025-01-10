<?php

namespace App\Helpers;

use Closure;

class Wrapper
{
    public static function wrapCall(Closure $closure, ?Closure $uponFailed = null, bool $defaultExceptionReporting = true)
    {
        $results = null;
        try {
            // If callback doesn't return anything, results will be null.
            $results = $closure();
        } catch (\Throwable $exception) {
            if ($defaultExceptionReporting) {
                report($exception);
            }

            if (! is_null($uponFailed)) {
                // Do something with the exception or change results var. If callback doesn't return anything, results
                // will be null.
                $results = $uponFailed($exception);
            }
        }

        return $results;
    }
}
