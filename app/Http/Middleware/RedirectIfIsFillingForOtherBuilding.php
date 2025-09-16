<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Helpers\Hoomdossier;
use Closure;
use Illuminate\Support\Facades\Log;

class RedirectIfIsFillingForOtherBuilding
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Hoomdossier::user();
        if ($user->isFillingToolForOtherBuilding()) {
            Log::debug('Wow, user id ' . $user->id . ' tried to do something fishy!');

            return redirect()->route('cooperation.frontend.tool.expert-scan.index', ['step' => 'ventilation']);
        }

        return $next($request);
    }
}
