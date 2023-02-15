<?php

namespace App\Services\Lvbag\Payloads;

class AddressExpanded
{

    public ?array $expendedAddress = null;

    public function __construct(?array $expendedAddress)
    {
        $this->expendedAddresses = $expendedAddress;
    }

    public function isEmpty(): bool
    {
        return empty($this->expendedAddress);
    }

    public function first(): ?array
    {
//        return array_shift($this->expendedAddresses);
    }

    public function prepareForBuilding(): array
    {
//        $address = $this->first();
        return  [
            'id' => $address['nummeraanduidingIdentificatie'] ?? '',
            'bag_woonplaats_id' => $address['woonplaatsIdentificatie'] ?? '',
            'street' => $address['openbareRuimteNaam'] ?? '',
            'number' => $address['huisnummer'] ?? '',
            'postal_code' => $address['postcode'] ?? '',
            // so this is incorrect, but ye
            'house_number_extension' => $houseNumberExtension,
            'city' => $address['woonplaatsNaam'] ?? '',
            'build_year' => $address['oorspronkelijkBouwjaar'][0] ?? 1930,
            'surface' => $address['oppervlakte'] ?? 0,
        ];
    }
}