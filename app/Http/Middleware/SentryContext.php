<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Account;
use Closure;
use Sentry\State\Scope;

class SentryContext
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
        if (auth()->check() && app()->bound('sentry')) {

            \Log::debug("Context:");
            \Log::debug(json_encode(HoomdossierSession::getAll()));

            \Log::debug("-- sentry scope --");
            /** @var Account $account */
            $account = Hoomdossier::account();
            $user = Hoomdossier::user();
            $building = HoomdossierSession::getBuilding(true);
            $inputSource = HoomdossierSession::getInputSource(true);
            $inputSourceValue = HoomdossierSession::getInputSourceValue(true);

            $u = [
                'account' => $account->id,
                'id' => $user->id,
                'role' => HoomdossierSession::currentRole(),
                'is_observing' => HoomdossierSession::isUserObserving() ? 'yes' : 'no',
                'is_comparing' => HoomdossierSession::isUserComparingInputSources() ? 'yes' : 'no',
                'input_source' => $inputSource->short,
                'operating_on_own_building' => $building->user->id == $user->id ? 'yes' : 'no',
                'operating_as' => $inputSourceValue->short,
            ];

            $tags = [
                'building:id' => $building->id,
                'building:owner' => $building->user->id,
            ];

            \Log::debug("User:");
            \Log::debug(json_encode($u));
            \Log::debug("Tags:");
            \Log::debug(json_encode($tags));

            \Log::debug("-- end sentry scope --");

            \Sentry\configureScope(function (Scope $scope) use($u, $tags) {
                $scope->setUser($u);
                $scope->setTags($tags);
            });
        }

        return $next($request);
    }
}
