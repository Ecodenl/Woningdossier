<?php

namespace App\Helpers;

use Ecodenl\PicoWrapper\PicoClient;
use Illuminate\Support\Facades\Log;

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
        \Log::debug($postalCode . ' ' . $number);

        /** @var PicoClient $pico */
        $pico = app()->make('pico');

        $postalCode = str_replace(' ', '', trim($postalCode));

        $response = null;

        try {
            $response = $pico->bag_adres_pchnr(['query' => ['pc' => $postalCode, 'hnr' => $number]]);
        } catch (\Exception $exception) {
            Log::debug(__METHOD__.' '.$exception->getMessage());
        }

        return $response;
    }

    /**
     * Returns the address data from pico in a more usable form.
     *
     * @param string $postalCode
     * @param string|int $number
     * @param string|null $houseNumberExtension Default: null
     *
     * @return array
     */
    public static function getAddressData($postalCode, $number, $houseNumberExtension = null): array
    {
        $options = collect(static::getBagAddressData($postalCode, $number))->keyBy('huisletter');
        $result = [];
        if ($options->isNotEmpty()) {
            // get the best address option for the result.
            if (empty($houseNumberExtension) || !isset($options[$houseNumberExtension])) {
                $best = 'None';
            } else {
                $best = $houseNumberExtension;
            }

            $option = [];
            if ($options->has($best)) {
                $option = $options->get($best);
            }

            // best match
            $result = [
                'id' => $option['bag_adresid'] ?? '',
                'street' => $option['straat'] ?? '',
                'number' => $option['huisnummer'] ?? '',
                'postal_code' => $option['postcode'] ?? '',
                'house_number_extension' => $houseNumberExtension,
                'city' => $option['woonplaats'] ?? '',
                'build_year' => $option['bouwjaar'] ?? '',
                'surface' => $option['adresopp'] ?? '',
            ];
        }

        return $result;
    }
}
