<?php

namespace App\Services\EpOnline;

use App\Helpers\Wrapper;
use App\Traits\Services\HasBuilding;
use App\Traits\Services\HasInputSources;
use Ecodenl\EpOnlinePhpWrapper\EpOnline;
use Ecodenl\EpOnlinePhpWrapper\Resources\PandEnergielabel;
use GuzzleHttp\Exception\ClientException;
use Throwable;

class EnergyLabelService
{
    use HasBuilding,
        HasInputSources;

    private PandEnergielabel $client;

    public function __construct(EpOnline $epOnline)
    {
        $this->client = $epOnline->pandEnergielabel();
    }

    public function getEnergyLabel(): ?string
    {
        $result = Wrapper::wrapCall(function () {
            if (! empty($this->building->bag_addressid)) {
                $result = $this->client->byId($this->building->bag_addressid);
            } else {
                // Fall back to address in case no BAG ID is available.
                $attributes = [
                    'postcode' => $this->getNormalizedZipcode(),
                    'huisnummer' => $this->building->number,
                ];

                if (! empty($this->building->extension)) {
                    $extension = $this->building->extension;

                    // Extension found, so can be multiple cases, just like in the BagService...
                    $result = $this->attemptFetchingEnergyLabelFromAddress($attributes + ['huisnummertoevoeging' => $extension]);

                    if (empty($result) && strlen($extension) === 1) {
                        $result = $this->attemptFetchingEnergyLabelFromAddress($attributes + ['huisletter' => $extension]);
                    }

                    if (empty($result)) {
                        $extensions = str_split($extension);
                        $filteredExtensions = [];
                        // huisletter should always have a length of 1
                        $huisletter = array_shift($extensions);
                        $huisnummertoevoeging = implode('', $extensions);

                        if (! empty($huisletter)) {
                            $filteredExtensions['huisletter'] = $huisletter;
                        }
                        if (! empty($huisnummertoevoeging)) {
                            $filteredExtensions['huisnummertoevoeging'] = $huisnummertoevoeging;
                        }
                        $result = $this->attemptFetchingEnergyLabelFromAddress($attributes + $filteredExtensions);
                    }
                } else {
                    // The simple case :)
                    $result = $this->attemptFetchingEnergyLabelFromAddress($attributes);
                }
            }

            return $result;
        }, function (Throwable $exception) {
            $throw = true;

            if ($exception instanceof ClientException) {
                // If no key given, 401 is thrown. If address isn't found, 404 is thrown.
                $throw = $exception->getCode() !== 401 && $exception->getCode() !== 404;
            }

            if ($throw) {
                report($exception);
            }

            return [];
        }, false);

        // Result is array wrapped but always only one result...
        return $result[0]['labelLetter'] ?? null;
    }

    private function attemptFetchingEnergyLabelFromAddress(array $attributes): array
    {
        return Wrapper::wrapCall(
            fn () => $this->client->byAddress($attributes),
            function (Throwable $exception) {
                $throw = true;

                if ($exception instanceof ClientException) {
                    // If no key given, 401 is thrown. If address isn't found, 404 is thrown.
                    $throw = $exception->getCode() !== 401 && $exception->getCode() !== 404;
                }

                if ($throw) {
                    report($exception);
                }

                return [];
            },
            false
        );
    }

    /**
     * Normalize the zipcode so it works with the EP API.
     */
    private function getNormalizedZipcode(): ?string
    {
        $zipcode = $this->building->postal_code;
        preg_match('/^(\d{4})\s?([a-zA-Z]{2})$/', $zipcode, $matches);

        if (! empty($matches)) {
            return $matches[1] . mb_strtoupper($matches[2]);
        }

        return '';
    }
}