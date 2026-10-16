<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\RoofType;

/**
 * The id of a roof type by its short. The ids differ per environment; the shorts do not.
 *
 * A class of its own for the same reason as ElementValues: a mapper reasons in shorts and stays
 * testable without a database.
 */
class RoofTypes
{
    public function idFor(string $short): ?int
    {
        $roofType = RoofType::findByShort($short);

        return $roofType instanceof RoofType ? $roofType->id : null;
    }
}
