<?php

namespace App\Services\Kengetallen;

use App\Services\Kengetallen\Resolvers\BuildingDefined;

use App\Services\Kengetallen\Resolvers\RvoDefined;
use App\Services\Kengetallen\Resolvers\KengetallenDefiner;
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
        RvoDefined::class,
    ];

    public function resolve(string $kengetallenCode)
    {
        foreach ($this->resolvers as $resolver) {
            $value = $this->get(new $resolver(), $kengetallenCode);
            if ( ! is_null($value)) {
                return $value;
            }
        }
    }

    public function get($resolver, string $kengetallenCode)
    {
        return (new $resolver)
            ->context([
                'building' => $this->building,
                'inputSource' => $this->inputSource,
            ])
            ->get($kengetallenCode);
    }

    public function explain(string $kengetallenCode): KengetallenDefiner
    {
        foreach ($this->resolvers as $resolver) {
            $resolver = new $resolver();
            $value = $this->get($resolver, $kengetallenCode);
            if ( ! is_null($value)) {
                return $resolver;
            }
        }
    }
}