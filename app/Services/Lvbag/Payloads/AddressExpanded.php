<?php

namespace App\Services\Lvbag\Payloads;

class AddressExpanded
{

    public array $expendedAddress = [];

    public function __construct(array $expendedAddress)
    {
        $this->expendedAddress = $expendedAddress;
    }
}