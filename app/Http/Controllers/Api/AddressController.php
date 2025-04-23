<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FillAddressRequest;
use App\Models\Building;
use App\Models\Cooperation;
use App\Services\AddressService;
use Illuminate\Database\Eloquent\Builder;

class AddressController extends Controller
{
    public function checkDuplicates(AddressService $addressService, FillAddressRequest $request, Cooperation $cooperation): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $postalCode = trim(strip_tags($data['postal_code']));
        $number = trim(strip_tags($data['number']));
        $houseNumberExtension = trim(strip_tags($data['extension'] ?? ''));

        $postalCodeNormalized = strtolower($addressService->normalizeZipcode($postalCode, true));
        $postalCodeNoSpace = strtolower($addressService->normalizeZipcode($postalCode, false));

        $result = $cooperation->buildings()->where(function (Builder $query) use ($postalCodeNormalized, $postalCodeNoSpace) {
            $query->whereRaw('LOWER(postal_code) = ?', [$postalCodeNormalized])
                ->orWhereRaw('LOWER(postal_code) = ?', [$postalCodeNoSpace]);
        })->where('number', $number)
            ->where('extension', $houseNumberExtension)
            ->select(['street', 'city'])
            ->get();

        return response()->json([
            'count' => $result->count(),
            'addresses' => $result->map(function (Building $building) {
                return "{$building->street}, {$building->city}";
            })
        ]);
    }
}
