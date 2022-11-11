<?php

namespace App\Services;

use Ecodenl\LvbagPhpWrapper\Client;
use Ecodenl\LvbagPhpWrapper\Lvbag;
use Illuminate\Support\Facades\Log;

class AddressService
{

    /**
     * Returns the address data from the wrapper in the way we want
     * Will always return the FIRST result
     *
     * @param $postalCode
     * @param $number
     * @param null|string $houseNumberExtension
     * @return array
     */
    public static function first($postalCode, $number, ?string $houseNumberExtension = ""): array
    {
        $addresses = [];
        $client = Client::init(config('hoomdossier.services.bag.secret'), 'epsg:28992');

        $attributes = [
            'postcode' => $postalCode,
            'huisnummer' => $number,
            // we always want a exact match, rather no result than wrong one.
            'exacteMatch' => true,
        ];
        if (!empty($houseNumberExtension)) {
            $attributes['huisletter'] = $houseNumberExtension;
        }

        try {
            $addresses = Lvbag::init($client)
                ->adresUitgebreid()
                ->list($attributes);
        } catch (\Exception $exception) {
            if($exception->getCode() !== 400) {
                app('sentry')->captureException($exception);
            }
        }


        $result = [];

        // only when the address is not null, else we will take the user his input.
        if (!is_null($addresses)) {
            $address = array_shift($addresses);
            // best match
            $result = [
                'id' => $address['nummeraanduidingIdentificatie'] ?? '',
                'street' => $address['openbareRuimteNaam'] ?? '',
                'number' => $address['huisnummer'] ?? '',
                'postal_code' => $address['postcode'] ?? '',
                'house_number_extension' => $address['huisletter'] ?? $houseNumberExtension,
                'city' => $address['woonplaatsNaam'] ?? '',
                'build_year' => $address['bouwjaar'] ?? 1930,
                'surface' => $address['oppervlakte'] ?? 0,
            ];
            Log::debug(__CLASS__, $result);
        }

        return $result;
    }
}