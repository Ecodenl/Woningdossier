<?php

namespace App\Http\Middleware;

use App\Models\Cooperation;
use App\Models\User;
use Closure;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = \Auth::account()->user();

        if (! $user) {
            \Log::debug(__METHOD__.' !user');

            return redirect()->route('index');
        }

        // Pull cooperation
        $cooperationId = \Session::get('cooperation');
        $cooperation = Cooperation::find($cooperationId);

        if ($user->hasRole('super-admin')) {
            // Super administrator
            return $next($request);
        }

        \Log::debug(__METHOD__.' user does not have role super-admin');
        if ($user->hasRole('cooperation-admin') && $user->cooperations->contains($cooperation)) {
            // Admin for cooperation
            return $next($request);
        }
        \Log::debug(__METHOD__.' user does not have role cooperation-admin');

        \Log::debug(__METHOD__.' is admin middleware is being used');

        return redirect()->route('index');
    }
}
