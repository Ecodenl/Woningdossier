<?php

namespace App\Services\Models;

use App\Helpers\MappingHelper;
use App\Models\Mapping;
use App\Models\Municipality;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
use App\Traits\FluentCaller;
use Illuminate\Support\Collection;

class MunicipalityService
{
    use FluentCaller;

    protected Municipality $municipality;

    public function forMunicipality(Municipality $municipality): self
    {
        $this->municipality = $municipality;
        return $this;
    }

    /**
     * Get all available municipalities from BAG.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableBagMunicipalities(): Collection
    {
        return Mapping::where(function ($query) {
            $query->whereNull('target_model_id')
                ->orWhere(function ($query) {
                    $query->where('target_model_type', Municipality::class)
                        ->where('target_model_id', $this->municipality->id);
                });
        })->where('type', MappingHelper::TYPE_BAG_MUNICIPALITY)
            ->get();
    }

    /**
     * Get all available municipalities from VerbeterJeHuis.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableVbjehuisMunicipalities(): Collection
    {
        $municipalities = RegulationService::init()->getFilters()['Cities'];

        $usedIds = Mapping::where('type', MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS)
            ->where('from_model_id', '!=', $this->municipality->id)
            ->pluck('target_data')
            ->pluck('Id')
            ->toArray();

        return collect($municipalities)->filter(function ($municipality) use ($usedIds) {
            return ! in_array($municipality['Id'], $usedIds);
        });
    }

    /**
     * Retrieve all BAG municipalities belonging to this municipality.
     *
     * @return \Illuminate\Support\Collection
     */
    public function retrieveBagMunicipalities(): Collection
    {
        return MappingService::init()
            ->target($this->municipality)
            ->type(MappingHelper::TYPE_BAG_MUNICIPALITY)
            ->retrieveResolvable();
    }

    /**
     * Retrieve the VerbeterJeHuis municipality belonging to this municipality.
     *
     * @return \App\Models\Mapping|null
     */
    public function retrieveVbjehuisMuncipality(): ?Mapping
    {
        return MappingService::init()
            ->from($this->municipality)
            ->type(MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS)
            ->resolveMapping()
            ->first();
    }
}