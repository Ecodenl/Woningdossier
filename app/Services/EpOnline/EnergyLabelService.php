<?php

namespace App\Services\EpOnline;

use App\Helpers\Str;
use App\Helpers\Wrapper;
use App\Models\BuildingFeature;
use App\Models\EnergyLabel;
use App\Models\InputSource;
use App\Traits\Services\HasBuilding;
use Ecodenl\EpOnlinePhpWrapper\EpOnline;
use Ecodenl\EpOnlinePhpWrapper\Resources\PandEnergielabel;
use GuzzleHttp\Exception\ClientException;
use Throwable;

class EnergyLabelService
{
    use HasBuilding;

    private PandEnergielabel $client;

    public function __construct(EpOnline $epOnline)
    {
        $this->client = $epOnline->pandEnergielabel();
    }

    public function getEnergyLabelByAddress(string $zipCode, string $houseNumber, ?string $extension = null): ?string
    {
        $attributes = [
            'postcode' => Str::normalizeZipCode($zipCode),
            'huisnummer' => $houseNumber,
        ];

        if (!empty($extension)) {
            // Extension found, so can be multiple cases, just like in the BagService...
            $result = $this->attemptFetchingEnergyLabel($attributes + ['huisnummertoevoeging' => $extension]);

            if (empty($result) && strlen($extension) === 1) {
                $result = $this->attemptFetchingEnergyLabel($attributes + ['huisletter' => $extension]);
            }

            if (empty($result)) {
                $extensions = str_split($extension);
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
                $result = $this->attemptFetchingEnergyLabel($attributes + $filteredExtensions);
            }
        } else {
            // The simple case :)
            $result = $this->attemptFetchingEnergyLabel($attributes);
        }

        // Result is array wrapped but always only one result...
        return $result[0]['labelLetter'] ?? null;
    }

    public function getEnergyLabel(): ?string
    {
        if (!empty($this->building->bag_addressid)) {
            $result = $this->attemptFetchingEnergyLabel(['id' => $this->building->bag_addressid]);
        }

        // So, byId does NOT give the same results... This means that a label can be found with a given address,
        // but not from a BAG ID which is returned from the given address. How that works...???
        if (empty($result)) {
            // Fall back to address in case no BAG ID is available.
            return $this->getEnergyLabelByAddress($this->getNormalizedZipcode(), $this->building->number, $this->building->extension);
        }

        // Result is array wrapped but always only one result...
        return $result[0]['labelLetter'] ?? null;
    }

    public function syncEnergyLabel(): void
    {
        $label = $this->getEnergyLabel();
        $hydratedLabel = $this->hydrateLabel($label);

        // Save label to external.
        BuildingFeature::withoutGlobalScopes()
            ->updateOrCreate(
                [
                    'building_id' => $this->building->id,
                    'input_source_id' => InputSource::external()->id,
                ],
                [
                    'energy_label_id' => $hydratedLabel->id,
                ]
            );

        // Manually update master label if it's empty.
        $masterFeature = BuildingFeature::forInputSource(InputSource::master())
            ->forBuilding($this->building)
            ->first();

        if (empty($masterFeature->energy_label_id)) {
            $masterFeature->update(['energy_label_id' => $hydratedLabel->id]);
        }
    }

    /**
     * Convert string label to EnergyLabel model. Falls back to "none".
     */
    private function hydrateLabel(?string $label): EnergyLabel
    {
        $label = strtoupper($label);

        // Convert A++ and A++++ like labels to just A.
        if (Str::startsWith($label, 'A')) {
            $label = 'A';
        }

        $model = EnergyLabel::where('name', $label)->first();

        if (!$model instanceof EnergyLabel) {
            $model = EnergyLabel::where('name', 'X')->first();
        }

        return $model;
    }

    /**
     * Attempts to fetch an energy label with given BAG ID or address attributes.
     * byId endpoint is used when attributes has 'id', else attributes is used for the byAddress endpoint.
     * Then, attributes will contain 'postcode' and 'huisnummer', and might contain 'huisletter' and
     * 'huisnummertoevoeging' conform BAG spec.
     */
    private function attemptFetchingEnergyLabel(array $attributes): array
    {
        return Wrapper::wrapCall(
            fn() => !empty($attributes['id']) ? $this->client->byId($attributes['id']) : $this->client->byAddress($attributes),
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
     * Normalize the zipcode for the EP API.
     *
     * @return string
     */
    private function getNormalizedZipcode(): string
    {
        return Str::normalizeZipCode($this->building->postal_code);
    }

}