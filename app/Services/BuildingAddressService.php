<?php

namespace App\Services;

use App\Events\BuildingAddressUpdated;
use App\Events\NoMappingFoundForBagMunicipality;
use App\Events\NoMappingFoundForVbjehuisMunicipality;
use App\Helpers\MappingHelper;
use App\Models\Building;
use App\Models\Municipality;
use App\Services\Lvbag\BagService;
use App\Traits\FluentCaller;
use Illuminate\Support\Arr;

class BuildingAddressService
{
    use FluentCaller;

    public ?Building $building;
    public BagService $bagService;
    public MappingService $mappingService;

    public function __construct(BagService $bagService, MappingService $mappingService)
    {
        $this->bagService = $bagService;
        $this->mappingService = $mappingService;
    }

    public function forBuilding(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    /**
     * Method speaks for itself... however ->
     * The var is prefixed with "fallback", this is because we want address data to use as a fallback.
     * (postal_code, number, extension, city, street)
     * We simply cant rely on external API data, the address data will always be filled with data from the request.
     * We only need it for the IDs.
     *
     * @param array $fallbackAddressData
     *
     * @return void
     */
    public function updateAddress(array $fallbackAddressData)
    {
        $addressExpanded = $this
            ->bagService
            ->addressExpanded(
                $fallbackAddressData['postal_code'],
                $fallbackAddressData['number'],
                $fallbackAddressData['extension']
            );

        $addressData = $addressExpanded->prepareForBuilding();

        $addressData = array_filter($addressData, function ($value, $key) {
            // filter out empty results, only for specific keys
            // we want to clear the bag values.
            if (! in_array($key, ['bag_addressid', 'bag_woonplaats_id', 'municipality_id'])) {
                return ! empty($value);
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH);

        // filter relevant data from the request
        $buildingData = Arr::only($fallbackAddressData, ['street', 'city', 'postal_code', 'number']);
        $buildingData = array_merge($buildingData, $addressData);
        // we will ALWAYS pick the extension from the fallback, BAG returns it in a format we dont use.
        $buildingData['extension'] = $fallbackAddressData['extension'] ?? '';

        $this->building->update($buildingData);
    }

    public function attachMunicipality(): void
    {
        // MUST be string! Empty string is ok.
        $bagWoonplaatsId = (string) $this->building->bag_woonplaats_id;
        // we cant rely on isDiry / wasChanged.
        $buildingAddressUpdated = false;

        $municipalityName = $this
            ->bagService
            ->showCity($bagWoonplaatsId, ['expand' => 'true'])
            ->municipalityName();

        // its entirely possible that a municipality is not returned from the bag.
        if (! is_null($municipalityName)) {
            $municipality = $this->mappingService
                ->from($municipalityName)
                ->type(MappingHelper::TYPE_BAG_MUNICIPALITY)
                ->resolveTarget()
                ->first();

            if ($municipality instanceof Municipality) {
                if ($this->building->municipality_id !== $municipality->id) {
                    $buildingAddressUpdated = true;
                }
                $this->building->municipality()->associate($municipality)->save();
                if ($this->mappingService->from($municipality)->type(MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS)->mappingDoesntExist()) {
                    NoMappingFoundForVbjehuisMunicipality::dispatch($municipality);
                }
            } else {
                // so the target is not resolved, that's "fine". We will check if a empty mapping exists
                // if not we will create it
                if ($this->mappingService->from($municipalityName)->type(MappingHelper::TYPE_BAG_MUNICIPALITY)->mappingDoesntExist()) {
                    NoMappingFoundForBagMunicipality::dispatch($municipalityName);
                }
                // The disassociate only matters when the field was filled before
                if (!is_null($this->building->municipality_id)) {
                    $buildingAddressUpdated = true;
                }
                // remove the relationship.
                $this->building->municipality()->disassociate()->save();
            }
        }
        // in the end it doesnt matter if the user dis or associated a municipality
        // we have to refresh its advices.
        if ($buildingAddressUpdated) {
            BuildingAddressUpdated::dispatch($this->building);
        }
    }
}
