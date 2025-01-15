<?php

namespace App\Helpers\Cache;

use App\Models\Step as StepModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Step extends BaseCache
{
    const string CACHE_KEY_GET_ORDERED = 'Step_getOrdered';
    const string CACHE_KEY_ALL_SLUGS = 'Step_allSlugs';

    public static function getOrdered(): Collection
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_GET_ORDERED),
            config('hoomdossier.cache.times.default'),
            function () {
                return StepModel::ordered()->get();
            }
        );
    }

    public static function allSlugs(): array
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_ALL_SLUGS),
            config('hoomdossier.cache.times.default'),
            function () {
                return StepModel::pluck('slug')->toArray();
            }
        );
    }
}
