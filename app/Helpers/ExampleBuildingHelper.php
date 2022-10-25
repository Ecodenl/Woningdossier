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
        'hr-boiler-comment',
        'heat-pump-comment',
        'sun-boiler-comment',
        'ventilation-comment',
        'wall-insulation-comment',
        'insulated-glazing-comment',
        'floor-insulation-comment',
        'roof-insulation-comment',
        'solar-panels-comment',
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
