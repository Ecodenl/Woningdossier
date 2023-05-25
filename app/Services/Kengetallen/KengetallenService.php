<?php

namespace App\Services\Kengetallen;

use App\Services\Kengetallen\Resolvers\BuildingDefined;

use App\Services\Kengetallen\Resolvers\CodeDefined;
use App\Traits\Services\HasBuilding;
use App\Traits\Services\HasInputSources;

class KengetallenService
{
    use HasBuilding, HasInputSources;

    public function resolve(string $kengetallenCode)
    {
        if ($value = $this->get(new CodeDefined(), $kengetallenCode)) {
            return $value;
        }
    }

    public function get($resolver, string $kengetallenCode)
    {
        if ($resolver instanceof BuildingDefined) {
            $value = (new $resolver)
                ->forBuilding($this->building)
                ->forInputSource($this->masterInputSource())
                ->get($kengetallenCode);

            if (!empty($value)) {
                return $value;
            }
        }

        if ($resolver instanceof CodeDefined) {
            $value = (new $resolver)
                ->get($kengetallenCode);
            if (!empty($value)) {
                return $value;
            }
        }
    }
}