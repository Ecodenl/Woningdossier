<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FillAddressRequest;
use App\Services\AddressService;
use App\Services\Lvbag\BagService;

class GeoController extends Controller
{
    public function getAddressData(BagService $bagService, FillAddressRequest $request)
    {
        $postalCode = trim(strip_tags($request->get('postal_code', '')));
        $number = trim(strip_tags($request->get('number', '')));
        $houseNumberExtension = trim(strip_tags($request->get('house_number_extension', '')));

        $address = $bagService
            ->addressExpanded($postalCode, $number, $houseNumberExtension)
            ->prepareForBuilding();

        if (empty($address)) {
            $address = $request->all();
        }

        return response()->json($address);
    }
}
