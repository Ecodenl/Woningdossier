<?php

namespace App\Services\Lvbag;

use App\Helpers\Str;
use App\Services\Lvbag\Payloads\AddressExpanded;
use App\Services\Lvbag\Payloads\City;
use App\Traits\FluentCaller;
use Ecodenl\LvbagPhpWrapper\Lvbag;

class BagService
{
    use FluentCaller;

    public Lvbag $lvbag;

    public function __construct(Lvbag $lvbag)
    {
        $this->lvbag = $lvbag;
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
    public function addressExpanded($postalCode, $number, ?string $houseNumberExtension = ""): AddressExpanded
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
        if (! empty($houseNumberExtension)) {
            $houseNumberExtension = $this->convertHouseNumberExtension($houseNumberExtension);

            $addressExpanded = $this->listAddressExpanded($attributes + ['huisnummertoevoeging' => $houseNumberExtension]);
            if ($addressExpanded->isEmpty()) {
                // if that does not work we will try the huislett
                $addressExpanded = $this->listAddressExpanded($attributes + ['huisletter' => $houseNumberExtension]);
            }

            // the extension may contain a combination fo the huisletter and toevoeging
            // we will handle that here
            if ($addressExpanded->isEmpty()) {
                $extensions = str_split($houseNumberExtension);
                // huisletter should always have a length of 1
                $huisletter = array_shift($extensions);
                $huisnummertoevoeging = implode('', $extensions);

                $addressExpanded = $this->listAddressExpanded($attributes + compact('huisletter',
                        'huisnummertoevoeging'));
            }

            // a last resort..
            if ($addressExpanded->isEmpty()) {
                // this is for the users that are not up to date on the huisletter and extension combi
                // they might only enter a huisleter or extension even though it should be a combi.
                // the previous calls were all based on a exact match, to get the best match.
                // this last resort turns that of to get the at least the build year accurate.
                $attributes['exacteMatch'] = false;
                // these 2 calls could both return multiple addresses
                // it just depends on the given address, we will shift it later on to get only one result
                // the surface is probably inaccurate however the build year will be spot on (i think :kek:)
                $addressExpanded = $this->listAddressExpanded($attributes + ['huisnummertoevoeging' => $houseNumberExtension]);

                if ($addressExpanded->isEmpty()) {
                    $addressExpanded = $this->listAddressExpanded($attributes + ['huisletter' => $houseNumberExtension]);
                }
                if ($addressExpanded->isEmpty()) {
                    // and a call without the extension, this should always return a address but this is very inaccurate.
                    $addressExpanded = $this->listAddressExpanded($attributes);
                }
            }
        } else {
            // the simple case.. :)
            $addressExpanded = $this->listAddressExpanded($attributes);
        }

        // so the bag MAY return it, split on huisletter and extension.
        // however it doesn't really matter since we will save it as is.
        $addressExpanded->expendedAddress['house_number_extension'] = $houseNumberExtension;

        return $addressExpanded;
    }

    public function showCity(string $woonplaatsIdentificatie, array $attributes = []): ?City
    {
        return new City($this->wrapCall(function () use ($woonplaatsIdentificatie, $attributes) {
            return $this->lvbag
                ->woonplaats()
                ->show($woonplaatsIdentificatie, $attributes);
        }));
    }

    public function listAddressExpanded(array $attributes): ?AddressExpanded
    {
        return new AddressExpanded($this->wrapCall(function () use ($attributes) {
            $list = $this->lvbag
                    ->adresUitgebreid()
                    ->list($attributes) ?? [];

            return array_shift($list);
        }));
    }

    public function wrapCall($closure): ?array
    {
        $result = [];
        try {
            $result = $closure();
            $result['endpoint_failure'] = false;
        } catch (\Exception $exception) {
            if ($exception->getCode() !== 200) {
                report($exception);
                $result['endpoint_failure'] = true;
            }
        }
        return $result;
    }

    public function convertHouseNumberExtension(string $extension): string
    {
        // The BAG only allows 4 letter extensions, however people don't always know the BAG named shorthand.
        // We convert the extension manually.
        $conversion = [
            'zwart' => 'zw',
        ];

        $key = Str::makeComparable($extension);
        return $conversion[$key] ?? $extension;
    }
}