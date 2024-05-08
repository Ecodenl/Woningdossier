<?php

namespace App\Services\Lvbag;

use App\Helpers\Str;
use App\Services\Lvbag\Payloads\AddressExpanded;
use App\Services\Lvbag\Payloads\City;
use App\Traits\FluentCaller;
use Ecodenl\LvbagPhpWrapper\Lvbag;
use Illuminate\Support\Facades\Log;

class BagService
{
    use FluentCaller;

    public Lvbag $lvbag;

    public function __construct(Lvbag $lvbag)
    {
        $this->lvbag = $lvbag;
    }

    public function getHouseNumberExtensions(string $postalCode, string $number): array
    {
        $attributes = [
            'postcode' => $postalCode,
            'huisnummer' => $number,
            'exacteMatch' => false,
        ];

        $result = [];
        $page = 0;

        // We don't know how many extensions an address has. Since pagination is limited to 100, we fetch as many as
        // we can, but if the result is 100, we will attempt another fetch since there _might_ be more.
        do {
            ++$page;
            $list = $this->wrapCall(fn () => $this->lvbag->adresUitgebreid()->page($page)->pageSize(100)->list($attributes) ?? []);
            $filtered = array_filter(array_unique(array_map(function ($address) {
                return trim(($address['huisletter'] ?? '') . ($address['huisnummertoevoeging'] ?? ''));
            }, $list)));
            $result = array_merge($result, $filtered);
        } while (count($filtered) === 100);

        natsort($result);

        return array_values($result);
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
            'exacteMatch' => true,
        ];

        // since we do not have a separate input for the huisletter we treat the houseNumberExtension as one:
        // first try it as a extension
        // if not found as a houseletter
        // if that does not work we will do a last resort that may not be that accurate..
        if (! empty($houseNumberExtension)) {
            $houseNumberExtension = $this->convertHouseNumberExtension($houseNumberExtension);

            $addressExpanded = $this->listAddressExpanded($attributes + ['huisnummertoevoeging' => $houseNumberExtension]);

            if ($addressExpanded->isEmpty() && strlen($houseNumberExtension) === 1) {
                // if that does not work we will try the huisletter (but only if it has length 1, it cannot be longer)
                $addressExpanded = $this->listAddressExpanded($attributes + ['huisletter' => $houseNumberExtension]);
            }

            // the extension may contain a combination fo the huisletter and toevoeging
            // we will handle that here
            if ($addressExpanded->isEmpty()) {
                $extensions = str_split($houseNumberExtension);
                $filteredExtensions = [];
                // huisletter should always have a length of 1
                $huisletter = array_shift($extensions);
                $huisnummertoevoeging = implode('', $extensions);

                if (!empty($huisletter)) {
                    $filteredExtensions['huisletter'] = $huisletter;
                }
                if (!empty($huisnummertoevoeging)) {
                    $filteredExtensions['huisnummertoevoeging'] = $huisnummertoevoeging;
                }
                $addressExpanded = $this->listAddressExpanded(
                    $attributes + $filteredExtensions
                );
            }
        } else {
            // the simple case.. :)
            $addressExpanded = $this->listAddressExpanded($attributes);
        }

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

    public function listAddressExpanded(array $attributes): AddressExpanded
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
                Log::error($exception->getMessage() .' '. $exception->getTraceAsString());
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