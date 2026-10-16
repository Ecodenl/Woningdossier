<?php

namespace App\Services\SmartTwin\Mapping\Elements;

/**
 * The part of a dwelling a measure works on: an element, and for the roof also its shape.
 *
 * Follows the measure's step rather than a list of measures. Every measure of a step works on the
 * same element, so a measure added to a step needs nothing here. The roof is the one place the step
 * is not enough: flat and pitched roofs have measures of their own. A measure that works on no
 * element at all is listed in WITHOUT_ELEMENT.
 */
final class ElementScope
{
    private const STEPS = [
        'wall-insulation'        => [Element::FACADE],
        'floor-insulation'       => [Element::FLOOR, Element::CRAWLSPACE],
        'roof-insulation'        => [Element::ROOF],
        'insulated-glazing'      => [Element::WINDOWS],
        'ventilation'            => [Element::VENTILATION],
        'high-efficiency-boiler' => [Element::HEATING, Element::HOT_WATER],
        'heating'                => [Element::HEATING, Element::HOT_WATER],
        'heater'                 => [Element::SOLAR_THERMAL],
        'solar-panels'           => [Element::PV],
    ];

    /**
     * Measures that work on no element, whatever their step: SmartTwin's dwelling model holds nothing
     * about sun shading or cooking. They sit on a step all the same, because every measure has one —
     * sun shading on the glazing, the induction hob on the heating — but holding them against that
     * step's element would store windows or heating details that have nothing to do with them.
     */
    public const WITHOUT_ELEMENT = ['sun-shading', 'induction-hob'];

    public function __construct(
        public readonly Element $element,
        public readonly ?RoofShape $shape = null,
    )
    {
    }

    /**
     * @return array<int, self>  Empty for a measure that works on no element in particular, such as
     *                           the small measures.
     */
    public static function forMeasure(string $measureShort, ?string $stepShort): array
    {
        // Bodemisolatie works on the crawlspace floor, not on the ground floor above it; holding it
        // against the floor would flag every one of them as a floor SmartTwin forgot to insulate.
        if ('bottom-insulation' === $measureShort) {
            return [new self(Element::CRAWLSPACE)];
        }

        if (in_array($measureShort, self::WITHOUT_ELEMENT, true)) {
            return [];
        }

        return array_map(
            fn (Element $element) => new self($element, Element::ROOF === $element ? self::roofShapeOf($measureShort) : null),
            self::STEPS[$stepShort] ?? [],
        );
    }

    public function covers(Element $element, ?RoofShape $shape = null): bool
    {
        return $this->element === $element
            && (is_null($shape) || is_null($this->shape) || $this->shape === $shape);
    }

    /**
     * Flat and pitched measures carry it in their short (roof-insulation-flat-current,
     * roof-insulation-pitched-inside, …). replace-tiles only exists for a pitched roof and
     * replace-roof-insulation only for a flat one. Anything else works on the roof as a whole.
     */
    private static function roofShapeOf(string $measureShort): ?RoofShape
    {
        return match (true) {
            str_contains($measureShort, 'flat'), 'replace-roof-insulation' === $measureShort => RoofShape::FLAT,
            str_contains($measureShort, 'pitched'), 'replace-tiles' === $measureShort        => RoofShape::PITCHED,
            default                                                                          => null,
        };
    }
}
