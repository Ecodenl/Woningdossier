<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
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
    ];
}
