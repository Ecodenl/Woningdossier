<?php

namespace App\Services\Econobis\Payloads;

use App\Models\InputSource;

trait MasterInputSource
{
    private InputSource $masterInputSource;

    public function __construct()
    {
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }
}
