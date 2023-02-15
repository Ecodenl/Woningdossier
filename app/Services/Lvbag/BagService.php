<?php

namespace App\Services\Lvbag;

use App\Services\Lvbag\Payloads\AddressExpanded;
use App\Traits\FluentCaller;
use Ecodenl\LvbagPhpWrapper\Client;
use Ecodenl\LvbagPhpWrapper\Lvbag;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class BagService
{
    use FluentCaller;

    public Client $client;

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
     * @param  null|string  $houseNumberExtension
     * @return array
     */
    public function address($postalCode, $number, ?string $houseNumberExtension = "")
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
        // if that does not work we will do a last resort that may not be that accurate..
        if ( ! empty($houseNumberExtension)) {
            $addresses = $this->listFromAttributes($attributes + ['huisnummertoevoeging' => $houseNumberExtension]);
            if ($addresses->isEmpty()) {
                // if that does not work we will try the huislett
                $addresses = $this->listFromAttributes($attributes + ['huisletter' => $houseNumberExtension]);
            }

            // the extension may contain a combination fo the huisletter and toevoeging
            // we will handle that here
            if ($addresses->isEmpty()) {
                $extensions = str_split($houseNumberExtension);
                // huisletter should always have a length of 1
                $huisletter = array_shift($extensions);
                $huisnummertoevoeging = implode('', $extensions);

                $addresses = $this->listFromAttributes($attributes + compact('huisletter', 'huisnummertoevoeging'));
            }

            // a last resort..
            if ($addresses->isEmpty()) {
                // this is for the users that are not up to date on the huisletter and extension combi
                // they might only enter a huisleter or extension even though it should be a combi.
                // the previous calls were all based on a exact match, to get the best match.
                // this last resort turns that of to get the at least the build year accurate.
                $attributes['exacteMatch'] = false;
                // these 2 calls could both return multiple addresses
                // it just depends on the given address, we will shift it later on to get only one result
                // the surface is probably inaccurate however the build year will be spot on (i think :kek:)
                $addresses = $this->listFromAttributes($attributes + ['huisnummertoevoeging' => $houseNumberExtension]);

                if ($addresses->isEmpty()) {
                    $addresses = $this->listFromAttributes($attributes + ['huisletter' => $houseNumberExtension]);
                }
                if ($addresses->isEmpty()) {
                    // and a call without the extension, this should always return a address but this is very inaccurate.
                    $addresses = $this->listFromAttributes($attributes);
                }
            }
        } else {
            // the simple case.. :)
            $addresses = $this->listFromAttributes($attributes);
        }

        $result = [];

        return $addresses;
    }

    public function firstWoonplaats($woonplaatsIdentificatie, array $attributes = []): ?array
    {
        return $this->wrapCall(fn() => Lvbag::init($this->client)
            ->woonplaats()
            ->show($woonplaatsIdentificatie, $attributes)
        );
    }


    public function listFromAttributes(array $attributes): ?AddressExpanded
    {
        return new AddressExpanded($this->wrapCall(fn() => Lvbag::init($this->client)
            ->adresUitgebreid()
            ->list($attributes)
        ));
    }

    public function wrapCall($closure): ?array
    {
        $result = null;
        try {
            $result = $closure();
        } catch (\Exception $exception) {
            if ($exception->getCode() !== 400) {
                app('sentry')->captureException($exception);
            }
            if ($exception->getCode() === 400) {
                return $result;
            }
        }
        return $result;
    }
}