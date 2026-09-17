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
        return $this->lookUp($elementShort, 'calculate_value', $calculateValue);
    }

    /**
     * The same, by display order.
     *
     * Only for elements whose calculate values do not tell their options apart. The crawlspace is
     * one: "Heel laag (minder dan 30 cm)" and "Onbekend" both carry 0, so a lookup by calculate
     * value would return whichever came first.
     */
    public function idForOrder(string $elementShort, int $order): ?int
    {
        return $this->lookUp($elementShort, 'order', $order);
    }

    private function lookUp(string $elementShort, string $column, int $value): ?int
    {
        $element = Element::findByShort($elementShort);

        if (! $element instanceof Element) {
            return null;
        }

        return $element->values()
            ->where($column, $value)
            ->value('id');
    }
}
