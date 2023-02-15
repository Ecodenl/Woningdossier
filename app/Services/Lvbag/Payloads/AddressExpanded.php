<?php

namespace App\Services\Lvbag\Payloads;

class AddressExpanded
{

    public ?array $expendedAddress = null;

    public function __construct(?array $expendedAddress)
    {
        $this->expendedAddress = $expendedAddress;
    }

    public function isEmpty(): bool
    {
        return empty($this->expendedAddress);
    }

    public function prepareForBuilding(): array
    {
        $address = $this->expendedAddress;
        return  [
            'bag_addressid' => $address['nummeraanduidingIdentificatie'] ?? '',
            'bag_woonplaats_id' => $address['woonplaatsIdentificatie'] ?? '',
            'street' => $address['openbareRuimteNaam'] ?? '',
            'number' => $address['huisnummer'] ?? '',
            'postal_code' => $address['postcode'] ?? '',
            'city' => $address['woonplaatsNaam'] ?? '',
            'build_year' => $address['oorspronkelijkBouwjaar'][0] ?? 1930,
            'surface' => $address['oppervlakte'] ?? 0,
        ];
    }
}