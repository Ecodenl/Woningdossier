<?php

namespace App\Traits;

trait DebugableMigrationTrait
{
    public function line($msg)
    {
        echo "{$msg} \r\n";
    }
}