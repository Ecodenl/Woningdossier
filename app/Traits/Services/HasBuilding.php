<?php

namespace App\Traits\Services;

use App\Models\Building;

trait HasBuilding
{
    public Building $building;

    public function forBuilding(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function lastDoneAt()
    {

    }
}