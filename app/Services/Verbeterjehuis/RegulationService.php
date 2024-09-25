<?php

namespace App\Services\Verbeterjehuis;

use App\Helpers\Cache\BaseCache;
use App\Helpers\MappingHelper;
use App\Models\Municipality;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\Payloads\Search;
use App\Traits\FluentCaller;
use App\Traits\Services\HasBuilding;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RegulationService
{
    use FluentCaller,
        HasBuilding;

    const SUBSIDY = 'subsidy';
    const LOAN = 'loan';
    const OTHER = 'other';

    public array $context = [];

    public function getFilters(): array
    {
        $cacheKey = BaseCache::getCacheKey('getFilters');
        $repository = Cache::driver('database');

        // If cache is empty, reset the cache. We do it like this to limit the required calls to the external API.
        // When the key has expired, `has()` will return false!
        if ($repository->has($cacheKey)) {
            if (empty($repository->get($cacheKey))) {
                $repository->forget($cacheKey);
            }
        }

        return $repository
            ->remember($cacheKey, Carbon::now()->addDay(), function () {
                return app(Verbeterjehuis::class)
                    ->regulation()
                    ->getFilters();
            });
    }

    public function getSearch(): ?Search
    {
        // we will get the user his municipality
        // then try to resolve the mapping and set it as a city.
        $municipality = $this->building->municipality;
        if ($municipality instanceof Municipality) {
            $target = MappingService::init()
                ->from($municipality)
                ->type(MappingHelper::TYPE_MUNICIPALITY_VBJEHUIS)
                ->resolveTarget()
                ->first();

            $cityId = $target['Id'] ?? null;

            // VerbeterJeHuis doesn't accept a null value.
            if (is_null($cityId)) {
                return null;
            }

            // TODO: If result is empty, it will be cached... Should we assume results are always correct?
            $this->context['cityId'] = $cityId;
            return Search::init(
                Cache::driver('database')->remember($this->getCacheKey(), Carbon::now()->addDay(), function () {
                    // Note: If the search method throws a exception it won't be cached.
                    return app(Verbeterjehuis::class)
                        ->regulation()
                        ->search($this->context);
                })
            );
        }

        return null;
    }

    private function getCacheKey(): string
    {
        return md5(implode('|', $this->context));
    }
}
