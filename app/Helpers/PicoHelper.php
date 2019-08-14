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
     * @param        $postalCode
     * @param        $number
     * @param  null  $houseNumberExtension
     *
     * @return array
     */
    public static function getAddressData($postalCode, $number, $houseNumberExtension = null): array
    {
        $options = collect(static::getBagAddressData($postalCode, $number))->keyBy('huisletter');

        // get the best address option for the result.
        if (empty($houseNumberExtension) || !isset($options[$houseNumberExtension])) {
            $option = $options['None'];
        } else {
            $option = $options[$houseNumberExtension];
        }

        // best match
        $result = [
            'id'                     => $option['bag_adresid'] ?? '',
            'street'                 => $option['straat'] ?? '',
            'number'                 => $option['huisnummer'] ?? '',
            'postal_code'            => $option['postcode'] ?? '',
                        'house_number_extension' => $houseNumberExtension,
            'city'                   => $option['woonplaats'] ?? '',
            'build_year'             => $option['bouwjaar'] ?? '',
            'surface'                => $option['adresopp'] ?? ''
        ];

        return $result;
    }
}