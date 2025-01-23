<?php

namespace App\Services\Lvbag\Payloads;

class AddressExpanded
{
    public array $expandedAddress = [];

    public function __construct(?array $expandedAddress)
    {
        $this->expandedAddress = $expandedAddress ?? [];
    }

    public function isEmpty(): bool
    {
        $expandedAddress = $this->expandedAddress;
        unset($expandedAddress['endpoint_failure']);
        return empty($expandedAddress);
    }

    public function prepareForBuilding(): array
    {
        $address = $this->expandedAddress;
        $data = [
            'street' => $address['openbareRuimteNaam'] ?? '',
            'number' => $address['huisnummer'] ?? '',
            'extension' => trim(($address['huisletter'] ?? '') . ($address['huisnummertoevoeging'] ?? '')),
            'postal_code' => $address['postcode'] ?? '',
            'city' => $address['woonplaatsNaam'] ?? '',
            'build_year' => $address['oorspronkelijkBouwjaar'][0] ?? 1930,
            'surface' => $address['oppervlakte'] ?? 0,
        ];

        // when there is no endpoint failure we can get the data from the endpoint.
        if ($this->expandedAddress['endpoint_failure'] == false) {
            $data['bag_addressid'] = $address['nummeraanduidingIdentificatie'] ?? '';
            $data['bag_woonplaats_id'] = $address['woonplaatsIdentificatie'] ?? '';

            if (empty($data['bag_woonplaats_id'])) {
                $data['municipality_id'] = null;
            }
        }

        return $data;
    }
}
