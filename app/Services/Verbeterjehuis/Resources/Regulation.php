<?php

namespace App\Services\Verbeterjehuis\Resources;

class Regulation extends Resource
{
    /**
     * Returns all available "filters", these are to be used on the search.
     */
    public function getFilters(): array
    {
        return $this->client->get($this->uri('getfilters'));
    }

    /**
     * Returns all available subsidies for the given attributes.
     */
    public function search(array $attributes): array
    {
        return $this->client->get($this->uri('search'), static::buildQuery($attributes));
    }
}