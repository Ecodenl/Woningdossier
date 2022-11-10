<?php

namespace App\Helpers;

use App\Services\AddressService;

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
        return AddressService::first($postalCode, $number, $houseNumberExtension);
    }
}
