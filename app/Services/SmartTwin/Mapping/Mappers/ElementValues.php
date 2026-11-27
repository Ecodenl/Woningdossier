<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Element;
use App\Models\ElementValue;
use App\Models\PvPanelOrientation;
use App\Models\Service;

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

    /**
     * The label of the option that was written, for the mapping report.
     *
     * Read back from the row itself rather than from what the mapping meant to pick, so the report
     * says what actually landed in the dossier.
     */
    public function labelFor(int $elementValueId): ?string
    {
        $elementValue = ElementValue::find($elementValueId);

        return $elementValue instanceof ElementValue ? $elementValue->getTranslation('value', 'nl') : null;
    }

    /**
     * The id of a service value (a heat pump type, a boiler, a kind of ventilation) by its order,
     * which every service numbers its options by.
     */
    public function serviceValueIdForOrder(string $serviceShort, int $order): ?int
    {
        $service = Service::findByShort($serviceShort);

        return $service instanceof Service ? $service->values()->where('order', $order)->value('id') : null;
    }

    /** The id of a panel orientation by its short. */
    public function orientationIdFor(string $short): ?int
    {
        return PvPanelOrientation::where('short', $short)->value('id');
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
