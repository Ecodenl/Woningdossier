<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class designed to help with the example building content.
 */
class ExampleBuildingHelper
{
    /**
     * All relevant sub steps for example buildings. ! This assumes the NL slug !
     *
     * @var array
     */
    const RELEVANT_SUB_STEPS = [
        'wat-voor-woning',
        'woning-type',
        'huur-of-koop', // Is this one relevant? No. Is it mixed in between and could otherwise cause weird behaviour? Yes. Surprise!
        'bouwjaar-en-oppervlak',
        'specifieke-voorbeeld-woning'
    ];

    /**
     * These are tool questions that should not be answerable when setting example building content
     */
    const UNANSWERABLE_TOOL_QUESTIONS = [
        'building-type-category',
        'building-type',
        'build-year',
        'specific-example-building',
        'building-data-comment-resident',
        'building-data-comment-coach',
        'usage-quick-scan-comment-resident',
        'usage-quick-scan-comment-coach',
        'living-requirements-comment-resident',
        'living-requirements-comment-coach',
        'residential-status-element-comment-resident',
        'residential-status-element-comment-coach',
        'residential-status-service-comment-resident',
        'residential-status-service-comment-coach',
        'hr-boiler-comment-resident',
        'hr-boiler-comment-coach',
        'heat-pump-comment-resident',
        'heat-pump-comment-coach',
        'sun-boiler-comment-resident',
        'sun-boiler-comment-coach',
        'ventilation-comment-resident',
        'ventilation-comment-coach',
        'wall-insulation-comment-resident',
        'wall-insulation-comment-coach',
        'insulated-glazing-comment-resident',
        'insulated-glazing-comment-coach',
        'floor-insulation-comment-resident',
        'floor-insulation-comment-coach',
        'roof-insulation-comment-resident',
        'roof-insulation-comment-coach',
        'solar-panels-comment-resident',
        'solar-panels-comment-coach',
        'resident-count',
        'amount-gas',
        'amount-electricity',
        'total-installed-power',
        'comfort-priority',
    ];

    public static function old($key, $default)
    {
        return Arr::dot(old())[$key] ?? $default;
    }
}
