<?php

namespace App\Traits;

use App\Helpers\Cache\BaseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait HasShortTrait
{
    /**
     * Find a record by its short.
     *
     * @param $short
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function findByShort($short): ?Model
    {
        $cacheKey = 'HasShortTrait_find_by_short_%s_%s';
        $className = get_class(self::getModel());

        return BaseCache::cacheModel(
            BaseCache::getCacheKey($cacheKey, $className, $short),
            self::whereShort($short)
        );
    }

    /**
     * Find multiple records by a set of shorts
     *
     * @param array  $shorts
     *
     * @return \Illuminate\Support\Collection
     */
    public static function findByShorts(array $shorts): Collection
    {
        return self::whereIn('short', $shorts)->get();
    }
}
