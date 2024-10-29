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
     * Retrieve all BAG municipalities belonging to this municipality.
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