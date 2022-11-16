<?php

namespace App\Services;

use App\Traits\FluentCaller;
use Ecodenl\LvbagPhpWrapper\Client;
use Ecodenl\LvbagPhpWrapper\Lvbag;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class AddressService
{
    public Client $client;

    use FluentCaller;

    public function __construct()
    {
        $this->client = Client::init(
            config('hoomdossier.services.bag.secret'),
            'epsg:28992',
            App::isProduction(),
        );
    }

    /**
     * Returns the address data from the wrapper in the way we want
     * Will always return the FIRST result
     *
     * @param $postalCode
     * @param $number
     * @param null|string $houseNumberExtension
     * @return array
     */
    public function first($postalCode, $number, ?string $houseNumberExtension = ""): array
    {
        $attributes = [
            'postcode' => $postalCode,
            'huisnummer' => $number,
            // we always want a exact match, rather no result than wrong one.
            'exacteMatch' => true,
        ];

        // since we do not have a separate input for the huisletter we treat the houseNumberExtension as one:
        // first try it as a extension
        // if not found as a houseletter
        // if that does not work try to split it and treat is as extension + letter combi
        if (!empty($houseNumberExtension)) {
            $addresses = $this->listFromAttributes($attributes + ['huisnummertoevoeging' => $houseNumberExtension]);
            if (is_null($addresses)) {
                // if that does not work we will try the huislett
                $addresses = $this->listFromAttributes($attributes + ['huisletter' => $houseNumberExtension]);
            }
            // a last resort..
            if (is_null($addresses)) {
                // so its still null, we will do a bolt assumption that the extension contains a combination of the
                // extension and letter, we will split it on "-" and send them both!
                list($huisletter, $huisnummertoevoeging) = explode('-', $houseNumberExtension);

                $addresses = $this->listFromAttributes($attributes + compact('huisletter', 'huisnummertoevoeging'));
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

    public function listFromAttributes(array $attributes): ?array
    {
        $addresses = null;
        try {
            $addresses = Lvbag::init($this->client)
                ->adresUitgebreid()
                ->list($attributes);
        } catch (\Exception $exception) {
            if ($exception->getCode() !== 400) {
                app('sentry')->captureException($exception);
            }
            if ($exception->getCode() === 400) {
                return $addresses;
            }
        }
        return $addresses;
    }
}