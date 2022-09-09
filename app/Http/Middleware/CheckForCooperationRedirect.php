<?php

namespace App\Http\Middleware;

use App\Models\Cooperation;
use App\Models\CooperationRedirect;
use Closure;

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
        $cooperation = $request->route()->parameter('cooperation');

        if (!$cooperation instanceof Cooperation) {

            if (!empty($cooperation)) {
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
