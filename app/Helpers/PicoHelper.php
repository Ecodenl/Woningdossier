<?php

namespace App\Helpers;

use App\Services\AddressService;
use App\Services\DiscordNotifier;

class PicoHelper
{

    /**
     * Returns the address data from pico in a more usable form.
     *
     * @param string $postalCode
     * @param string|int $number
     * @param string|null $houseNumberExtension Default: null
     *
     * @deprecated
     */
    public static function getAddressData($postalCode, $number, string $houseNumberExtension = ""): array
    {
        DiscordNotifier::init()->notify("PicoHelper::getAddressData() has been called, args: {$postalCode}, {$number}, $houseNumberExtension");
        return AddressService::init()->first($postalCode, $number, $houseNumberExtension);
    }
}
