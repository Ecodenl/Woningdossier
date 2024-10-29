<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;
use Illuminate\Support\Facades\URL;
use Closure;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
    ];

    /**
     * Handle an incoming request. Remove the full handle method after go-live.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If we're not in maintenance mode, there's no point in trying to find a cooperation
        if ($this->app->isDownForMaintenance()) {
            $host = $_SERVER['HTTP_HOST'];
            preg_match('/(?:http[s]*\:\/\/)*(.*?)\.((?=[^\/]*\..{2,5})|(?=localhost:[\d]{4}))/i', $host, $match);
            $cooperation = $match[1] ?? '';

            $cooperation = Cooperation::where('slug', '=', $cooperation)->first();

            if ($cooperation instanceof Cooperation) {
                HoomdossierSession::setCooperation($cooperation);

                // Set as default URL parameter
                if (HoomdossierSession::hasCooperation()) {
                    URL::defaults(['cooperation' => $cooperation->slug]);
                }
            }
        }

        return parent::handle($request, $next);
    }
}
