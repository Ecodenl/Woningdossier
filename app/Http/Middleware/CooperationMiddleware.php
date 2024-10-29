<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use Closure;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CooperationMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->query('test') == "1") {
            Log::channel('single')->debug(__METHOD__);
        }
        $cooperation = $request->route()->parameter('cooperation');

        // if no valid cooperation is found, return to index
        if (! $cooperation instanceof Cooperation) {
            return redirect()->route('index');
        }

        HoomdossierSession::setCooperation($cooperation);

        // Set as default URL parameter
        if (HoomdossierSession::hasCooperation()) {
            URL::defaults(['cooperation' => $cooperation->slug]);
        }
        return $next($request);
    }
}
