<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PicoHelper;
use App\Http\Requests\FillAddressRequest;
use App\Http\Controllers\Controller;

class GeoController extends Controller
{

    public function getAddressData(FillAddressRequest $request)
    {
        $postalCode = trim(strip_tags($request->get('postal_code', '')));
        $number = trim(strip_tags($request->get('number', '')));
        $houseNumberExtension = trim(strip_tags($request->get('house_number_extension', '')));

        $address = PicoHelper::getAddressData($postalCode, $number, $houseNumberExtension);

        return response()->json($address);
    }

}
