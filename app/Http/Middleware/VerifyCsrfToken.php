<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'tool/wall-insulation/calculate',
        'tool/insulated-glazing/calculate',
        'tool/floor-insulation/calculate',
        'tool/roof-insulation/calculate',
        'tool/high-efficiency-boiler/calculate',
        'tool/solar-panels/calculate',
        'tool/heater/calculate',
        'tool/example-building',
    ];
}
