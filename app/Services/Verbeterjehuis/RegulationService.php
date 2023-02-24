<?php

namespace App\Services\Verbeterjehuis;

use App\Helpers\Cache\BaseCache;
use App\Helpers\MappingHelper;
use App\Models\Building;
use App\Models\Municipality;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\Payloads\Search;
use App\Traits\FluentCaller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RegulationService
{
    use FluentCaller;

    const SUBSIDY = 'subsidy';
    const LOAN = 'loan';
    const OTHER = 'other';

    public Building $building;

    public array $context = [];

    public function forBuilding(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function getFilters(): array
    {
        if (Cache::driver('database')->has('getFilters')) {
            return Cache::driver('database')->get('getFilters');
        }

        // We will capture the exception here so the code doesn't get too bloated, however we will return
        // an empty array, which indicates something is wrong.
        try {
            $results = Verbeterjehuis::init(Client::init())
                ->regulation()
                ->getFilters();
        } catch (\Exception $e) {
            $results = [];
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        }

        // We won't cache the results if they're empty.
        if (empty($results)) {
            return [];
        }

        return Cache::driver('database')
            ->remember(BaseCache::getCacheKey('getFilters'), Carbon::now()->addDay(), fn () => $results);
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

            if (is_null($cityId)) {
                return null;
            }

            $this->context['cityId'] = $cityId;
            return Search::init(
                Cache::driver('database')->remember($this->getCacheKey(), Carbon::now()->addDay(), function () {
                    return Verbeterjehuis::init(Client::init())
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
