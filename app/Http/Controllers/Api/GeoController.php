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
        $extension = trim(strip_tags($request->get('house_number_extension', '')));

        $options = PicoHelper::getAddressData($postalCode, $number);

        $result = [];
        $dist = null;

        if (is_array($options) && count($options) > 0) {
            foreach ($options as $option) {
                $houseNumberExtension = (! empty($option['huisnrtoev']) && 'None' != $option['huisnrtoev']) ? $option['huisnrtoev'] : '';

                $newDist = null;
                if (! empty($houseNumberExtension) && ! empty($extension)) {
                    $newDist = levenshtein(strtolower($houseNumberExtension), strtolower($extension), 1, 10, 1);
                }
                if ((is_null($dist) || isset($newDist) && $newDist < $dist) && is_array($option)) {
                    // best match
                    $result = [
                        'id'                     => array_key_exists('bag_adresid', $option) ? md5($option['bag_adresid']) : '',
                        'street'                 => array_key_exists('straat', $option) ? $option['straat'] : '',
                        'number'                 => array_key_exists('huisnummer', $option) ? $option['huisnummer'] : '',
                        'house_number_extension' => $houseNumberExtension,
                        'city'                   => array_key_exists('woonplaats', $option) ? $option['woonplaats'] : '',
                    ];
                    $dist = $newDist;
                }
            }
        }

        return response()->json($result);
    }

}
