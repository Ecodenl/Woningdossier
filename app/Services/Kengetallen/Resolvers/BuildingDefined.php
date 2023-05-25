<?php

namespace App\Services\Kengetallen\Resolvers;

use App\Traits\Services\HasBuilding;
use App\Traits\Services\HasInputSources;

class BuildingDefined implements KengetallenResolver
{
    use HasBuilding, HasInputSources;

    public function get($kengetallenCode): float
    {
        dd('not done');
    }
}