<?php

namespace App\Services\Kengetallen;

use App\Services\Kengetallen\Resolvers\BuildingDefined;

use App\Services\Kengetallen\Resolvers\CodeDefined;
use App\Traits\Services\HasBuilding;
use App\Traits\Services\HasInputSources;

class KengetallenService
{
    use HasBuilding, HasInputSources;

    /**
     * The resolvers, sorted based on priority
     *
     * @var array|string[]
     */
    public array $resolvers = [
        BuildingDefined::class,
        CodeDefined::class
    ];

    public function resolve(string $kengetallenCode)
    {
        foreach ($this->resolvers as $resolver) {
            $value = $this->get(new $resolver(), $kengetallenCode);
            if (!is_null($value)) {
                return $value;
            }
        }
    }

    public function get($resolver, string $kengetallenCode)
    {
        if ($resolver instanceof BuildingDefined) {
            $value = (new $resolver)
                ->context([
                    'building' => $this->building,
                    'inputSource' => $this->inputSource,
                ])
                ->get($kengetallenCode);

            if ( ! empty($value)) {
                return $value;
            }
        }

        if ($resolver instanceof CodeDefined) {
            $value = (new $resolver)->get($kengetallenCode);
            if ( ! empty($value)) {
                return $value;
            }
        }
    }
}