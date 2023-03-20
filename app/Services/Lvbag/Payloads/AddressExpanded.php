<?php

namespace App\Services\Lvbag\Payloads;

class AddressExpanded
{
    // TODO: Fix typo, should be expAnded
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
        $data = [
            'street' => $address['openbareRuimteNaam'] ?? '',
            'number' => $address['huisnummer'] ?? '',
            'postal_code' => $address['postcode'] ?? '',
            'city' => $address['woonplaatsNaam'] ?? '',
            'build_year' => $address['oorspronkelijkBouwjaar'][0] ?? 1930,
            'surface' => $address['oppervlakte'] ?? 0,
        ];

        // when there is no endpoint failure we can get the data from the endpoint.
        if ($this->expendedAddress['endpoint_failure'] == false) {
            $data['bag_addressid'] = $address['nummeraanduidingIdentificatie'] ?? '';
            $data['bag_woonplaats_id'] = $address['woonplaatsIdentificatie'] ?? '';

            if (empty($data['bag_woonplaats_id'])) {
                $data['municipality_id'] = null;
            }
        }

        return $data;
    }
}