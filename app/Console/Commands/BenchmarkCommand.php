<?php

namespace App\Console\Commands;

trait BenchmarkCommand
{
    private float $startTime;

    private function startTimer()
    {
        $this->startTime = microtime(true);
    }

    private function stopTimer()
    {
        return round(microtime(true) - $this->startTime, 4);
    }
}