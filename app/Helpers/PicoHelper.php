<?php

namespace App\Helpers;

use Ecodenl\PicoWrapper\PicoClient;

class PicoHelper
{

    /**
     * Returns the raw address data from pico.
     *
     * @param $postalCode
     * @param $number
     *
     * @return array
     */
    public static function getBagAddressData($postalCode, $number)
    {
        \Log::debug($postalCode.' '.$number);

        /** @var PicoClient $pico */
        $pico = app()->make('pico');

        $postalCode = str_replace(' ', '', trim($postalCode));

        $response = $pico->bag_adres_pchnr(['query' => ['pc' => $postalCode, 'hnr' => $number]]);

        return $response;
    }


    /**
     * Returns the address data from pico in a more usable form.
     *
     * @param $postalCode
     * @param $number
     *
     * @return array
     */
    public static function getAddressData($postalCode, $number): array
    {
        $options = static::getBagAddressData($postalCode, $number);

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
                        'id'                     => $option['bag_adresid'] ?? '',
                        'street'                 => $option['straat'] ?? '',
                        'number'                 => $option['huisnummer'] ?? '',
                        'house_number_extension' => $houseNumberExtension,
                        'city'                   => $option['woonplaats'] ?? '',
                        'build_year'             => $option['bouwjaar'] ?? '',
                        'surface'                => $option['adresopp'] ?? ''
                    ];
                    $dist = $newDist;
                }
            }
        }

        return $result;
    }


//    public function fillAddress(FillAddressRequest $request)
//    {
//        $postalCode = trim(strip_tags($request->get('postal_code', '')));
//        $number = trim(strip_tags($request->get('number', '')));
//        $extension = trim(strip_tags($request->get('house_number_extension', '')));
//
//        $options = $this->getAddressData($postalCode, $number);
//        $result = [];
//        $dist = null;
//        if (is_array($options) && count($options) > 0) {
//            foreach ($options as $option) {
//                $houseNumberExtension = (! empty($option['huisnrtoev']) && 'None' != $option['huisnrtoev']) ? $option['huisnrtoev'] : '';
//
//                $newDist = null;
//                if (! empty($houseNumberExtension) && ! empty($extension)) {
//                    $newDist = levenshtein(strtolower($houseNumberExtension), strtolower($extension), 1, 10, 1);
//                }
//                if ((is_null($dist) || isset($newDist) && $newDist < $dist) && is_array($option)) {
//                    // best match
//                    $result = [
//                        'id'                     => array_key_exists('bag_adresid', $option) ? md5($option['bag_adresid']) : '',
//                        'street'                 => array_key_exists('straat', $option) ? $option['straat'] : '',
//                        'number'                 => array_key_exists('huisnummer', $option) ? $option['huisnummer'] : '',
//                        'house_number_extension' => $houseNumberExtension,
//                        'city'                   => array_key_exists('woonplaats', $option) ? $option['woonplaats'] : '',
//                    ];
//                    $dist = $newDist;
//                }
//            }
//        }
//
//        return response()->json($result);
//    }
//
//    protected function getAddressData($postalCode, $number, $pointer = null)
//    {
//        \Log::debug($postalCode.' '.$number.' '.$pointer);
//
//        /** @var PicoClient $pico */
//        $pico = app()->make('pico');
//
//        $postalCode = str_replace(' ', '', trim($postalCode));
//
//        $response = $pico->bag_adres_pchnr(['query' => ['pc' => $postalCode, 'hnr' => $number]]);
//
//        if (! is_null($pointer)) {
//            foreach ($response as $addrInfo) {
//                if (array_key_exists('bag_adresid', $addrInfo) && $pointer == md5($addrInfo['bag_adresid'])) {
//                    \Log::debug(json_encode($addrInfo));
//                    return $addrInfo;
//                }
//            }
//
//            return [];
//        }
//
//        return $response;
//    }
}