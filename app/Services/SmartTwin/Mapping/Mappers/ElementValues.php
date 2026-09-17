<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Element;

/**
 * Looks up the id of an element value by the ordinal the mapping reasons in.
 *
 * Questions like current-wall-insulation are answered with an element value id, which differs per
 * environment, while the mapping thinks in calculate values, which do not. This is the one step
 * between the two — kept apart so a mapper's reasoning stays testable without a database.
 */
class ElementValues
{
    public function idFor(string $elementShort, int $calculateValue): ?int
    {
        $element = Element::findByShort($elementShort);

        if (! $element instanceof Element) {
            return null;
        }

        return $element->values()
            ->where('calculate_value', $calculateValue)
            ->value('id');
    }
}
