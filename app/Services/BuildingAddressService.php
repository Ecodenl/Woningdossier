<?php

namespace App\Services;

use App\Events\BuildingAddressUpdated;
use App\Events\NoMappingFoundForBagMunicipality;
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
     * We simply cant rely on external api data, the address data will always be filled with data from the request
     * we only need it for the id's
     * @param  array  $fallbackAddressData
     * @return void
     */
    public function updateAddress(array $fallbackAddressData)
    {
        $addressData = $this
            ->bagService
            ->firstAddress(
                $fallbackAddressData['postal_code'],
                $fallbackAddressData['number'],
                $fallbackAddressData['extension']
            );
        // some fields will be left empty when there is no result.
        $addressData = array_filter($addressData);

        // filter relevant data from the request
        $buildingData = Arr::only($fallbackAddressData, ['street', 'city', 'postal_code', 'number']);
        $buildingData = array_merge($buildingData, $addressData);
        // we will ALWAYS pick the extension from the fallback, BAG returns it in a format we dont use.
        $buildingData['extension'] = $fallbackAddressData['extension'] ?? '';

        $this->building->update($buildingData);
    }

    public function attachMunicipality()
    {
        // MUST be string! Empty string is ok.
        $bagWoonplaatsId = (string)$this->building->bag_woonplaats_id;

        $municipalityName = $this
            ->bagService
            ->showCity($bagWoonplaatsId, ['expand' => 'true'])
            ->municipalityName();

        // its entirely possible that a municipality is not returned from the bag.
        if ( ! is_null($municipalityName)) {
            $municipality = $this->mappingService
                ->from($municipalityName)
                ->type(MappingHelper::TYPE_BAG_MUNICIPALITY)
                ->resolveTarget()
                ->first();


            if ($municipality instanceof Municipality) {
                $this->building->municipality()->associate($municipality)->save();
            } else {
                // so the target is not resolved, thats "fine". We will check if a empty mapping exists
                // if not we will create it
                if ($this->mappingService->from($municipalityName)->doesntExist()) {
                    NoMappingFoundForBagMunicipality::dispatch($municipalityName);
                }
                // remove the relationship.
                $this->building->municipality()->disassociate()->save();
            }
        }
        // in the end it doesnt matter if the user dis or associated a municipality
        // we have to refresh its advices.
        BuildingAddressUpdated::dispatch($this->building);
    }
}
