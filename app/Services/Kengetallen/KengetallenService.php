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
     */
    public array $resolvers = [
        BuildingDefined::class,
        RvoDefined::class,
    ];

    /**
     * Resolves a kengetallen value.
     */
    public function resolve(string $kengetallenCode): float
    {
        foreach ($this->resolvers as $resolver) {
            $value = $this->get(new $resolver(), $kengetallenCode);
            if (! is_null($value)) {
                return $value;
            }
        }

        // Technically this is ALWAYS the case since it's the final clause in the loop, but we add it anyway
        // so Larastan doesn't cry.
        return $this->get(new RvoDefined(), $kengetallenCode);
    }

    /**
     * Returns the value for the given code on the resolver.
     */
    public function get(KengetallenDefiner $resolver, string $kengetallenCode): ?float
    {
        return $resolver
            ->context([
                'building' => $this->building,
                'inputSource' => $this->inputSource,
            ])
            ->get($kengetallenCode);
    }

    /**
     * Returns what resolver is used a specific kengetallen code.
     */
    public function explain(string $kengetallenCode): KengetallenDefiner
    {
        foreach ($this->resolvers as $resolver) {
            $resolver = new $resolver();
            $value = $this->get($resolver, $kengetallenCode);
            if (! is_null($value)) {
                return $resolver;
            }
        }

        // Technically this is ALWAYS the case since it's the final clause in the loop, but we add it anyway
        // so Larastan doesn't cry.
        return new RvoDefined();
    }
}
