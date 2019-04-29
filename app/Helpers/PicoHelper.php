<?php

namespace App\Helpers;

use App\Http\Requests\FillAddressRequest;
use Ecodenl\PicoWrapper\PicoClient;

class PicoHelper
{

    public static function getAddressData($postalCode, $number, $pointer = null)
    {
        \Log::debug($postalCode.' '.$number.' '.$pointer);

        /** @var PicoClient $pico */
        $pico = app()->make('pico');

        $postalCode = str_replace(' ', '', trim($postalCode));

        $response = $pico->bag_adres_pchnr(['query' => ['pc' => $postalCode, 'hnr' => $number]]);

        if ( ! is_null($pointer)) {
            foreach ($response as $addrInfo) {
                if (array_key_exists('bag_adresid', $addrInfo) && $pointer == md5($addrInfo['bag_adresid'])) {
                    //$data['bag_addressid'] = $addrInfo['bag_adresid'];
                    \Log::debug(json_encode($addrInfo));

                    return $addrInfo;
                }
            }

            return [];
        }

        return $response;
    }
}