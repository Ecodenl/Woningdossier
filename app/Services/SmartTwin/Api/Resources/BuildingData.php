<?php

namespace App\Services\SmartTwin\Api\Resources;

use GuzzleHttp\RequestOptions;

class BuildingData extends Resource
{
    /**
     * The basic data SmartTwin has on an address: building type, construction year, living space
     * area and registered energy label, plus whether SmartTwin supports the address at all.
     *
     * @param  array{postal_code: string, house_number: int, house_number_addition?: string, house_letter?: string}  $address
     * @return array<string, mixed>  A BasicDataByAddressResponseModel.
     */
    public function basicDataByAddress(array $address): array
    {
        return $this->client->get($this->uri('basic-data-by-address'), [
            RequestOptions::QUERY => $address,
        ]);
    }
}
