<?php

namespace App\Services\Models;

use App\Helpers\MappingHelper;
use App\Models\Mapping;
use App\Models\Municipality;
use App\Services\MappingService;
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

    public function retrieveBagMunicipalities(): Collection
    {
        return MappingService::init()->type(MappingHelper::TYPE_BAG_MUNICIPALITY)->retrieveResolvable();
    }

    public function retrieveVbjehuisMuncipality(): ?Mapping
    {
        return MappingService::init()->type(MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS)->resolveMapping()->first();
    }
}