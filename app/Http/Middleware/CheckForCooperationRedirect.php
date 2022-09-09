<?php

namespace App\Http\Middleware;

use App\Models\Cooperation;
use App\Models\CooperationRedirect;
use Closure;
use Illuminate\Support\Facades\Log;

class CheckForCooperationRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $cooperation = $request->route('cooperation');

        if (!$cooperation instanceof Cooperation) {
            Log::debug("DBG cooperation is not an instance of Cooperation");
            if (!empty($cooperation)) {
                Log::debug("DBG cooperation is not empty ( = '" . $cooperation . "')");
                $redirect = CooperationRedirect::from($cooperation)->first();

                if ($redirect instanceof CooperationRedirect) {
                    return redirect(
                        str_ireplace(
                            $cooperation,
                            $redirect->cooperation->slug,
                            $request->url()
                        )
                    );
                }
            }
        }

        return $next($request);
    }
}
