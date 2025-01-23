<?php

namespace App\Traits;

use App\Helpers\Cache\BaseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasShortTrait
{
    /**
     * Find a record by its short.
     */
    public static function findByShort(string $short): ?static
    {
        $cacheKey = 'HasShortTrait_find_by_short_%s_%s';
        $className = get_class(static::getModel());

        return BaseCache::cacheModel(
            BaseCache::getCacheKey($cacheKey, $className, $short),
            static::whereShort($short)
        );
    }

    /**
     * Find multiple records by a set of shorts
     */
    public static function findByShorts(array $shorts): Collection
    {
        return static::whereIn('short', $shorts)->get();
    }

    public static function clearShortCache(string $short): void
    {
        $cacheKey = 'HasShortTrait_find_by_short_%s_%s';
        $className = get_class(static::getModel());

        BaseCache::clear(BaseCache::getCacheKey($cacheKey, $className, $short));
    }
}
