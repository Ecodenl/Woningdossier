<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Illuminate\Support\Facades\URL;
use Closure;
use Illuminate\Support\Str;

class CheckForMaintenanceMode extends Middleware
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
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $host = $_SERVER['HTTP_HOST'];
        preg_match('/(?:http[s]*\:\/\/)*(.*?)\.(?=[^\/]*\..{2,5})/i', $host, $match);
        $cooperation = $match[1] ?? '';

        if (empty($cooperation) && ! Str::contains(strtolower($host), 'http')) {
            // Why does this URL not have HTTP? We don't know. Probably local development in Docker
            // Grab cooperation as first element of the host instead
            $cooperation = explode('.', $host)[0] ?? '';
        }

        $cooperation = Cooperation::where('slug', '=', $cooperation)->first();

        // if no valid cooperation is found, return to index
        if (!$cooperation instanceof Cooperation) {
            return redirect()->route('index');
        }

        HoomdossierSession::setCooperation($cooperation);

        // Set as default URL parameter
        if (HoomdossierSession::hasCooperation()) {
            URL::defaults(['cooperation' => $cooperation->slug]);
        }

        return parent::handle($request, $next);
    }
}
