<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use Closure;
use Illuminate\Support\Facades\Log;

class RedirectIfIsFillingForOtherBuilding
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Hoomdossier::user();
        if ($user->isFillingToolForOtherBuilding()) {
            Log::debug('Wow, user id '.$user->id.' tried to do something fishy!');

            return redirect()->route('cooperation.frontend.tool.expert-scan.index', ['step' => 'ventilation']);
            //return redirect()->route('cooperation.tool.ventilation.index');
//            return redirect()->route('cooperation.tool.general-data.index');
        }

        return $next($request);
    }
}
