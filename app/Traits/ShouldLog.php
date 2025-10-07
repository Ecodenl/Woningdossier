<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait ShouldLog
{
    protected bool $isCalculations = false;

    public function log(string $line, array $data = [], string $method = 'debug'): void
    {
        if ($this->isCalculations) {
            if (config('hoomdossier.services.enable_calculation_logging')) {
                Log::channel('calculations')->{$method}($line, $data);
            }
        } else {
            if (config('hoomdossier.services.enable_logging')) {
                Log::{$method}($line, $data);
            }
        }
    }
}
