<?php

namespace App\Services;

use App\Services\Lvbag\BagService;
use App\Traits\FluentCaller;

class AddressService
{
    use FluentCaller;

    /**
     * @deprecated
     */
    public function first($postalCode, $number, ?string $houseNumberExtension = ""): array
    {
        DiscordNotifier::init()->notify('AddressService has been called, its deprecated.');
        return BagService::init()->firstAddress($postalCode, $number, $houseNumberExtension);
    }

}