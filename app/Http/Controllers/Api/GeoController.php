<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FillAddressRequest;
use App\Services\Lvbag\BagService;

class GeoController extends Controller
{
    public function getAddressData(BagService $bagService, FillAddressRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $postalCode = trim(strip_tags($data['postal_code']));
        $number = trim(strip_tags($data['number']));
        $houseNumberExtension = trim(strip_tags($data['extension'] ?? ''));

        $address = $bagService
            ->addressExpanded($postalCode, $number, $houseNumberExtension)
            ->prepareForBuilding();

        if ($request->has('fetch_extensions')) {
            $address['available_extensions'] = $bagService->getHouseNumberExtensions($postalCode, $number);
        }

        return response()->json($address);
    }
}
