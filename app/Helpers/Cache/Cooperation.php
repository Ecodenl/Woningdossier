<?php

namespace App\Helpers\Cache;

use Illuminate\Support\Collection;

class Cooperation extends BaseCache
{
    const CACHE_KEY_FIND = 'Cooperation_find_%s';
    const CACHE_KEY_GET_STYLE = 'Cooperation_getStyle_%s';
    const CACHE_KEY_GET_ACTIVE_ORDERED_STEPS = 'Cooperation_getActiveOrderedSteps_%s';

    /**
     * @param int $id
     *
     * @return \App\Models\Cooperation|null
     */
    public static function find($id)
    {
        return \Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\Cooperation::where('id', '=', $id)->with('style')->first();
            }
        );
    }

    public static function getStyle($cooperation)
    {
        if (! $cooperation instanceof \App\Models\Cooperation) {
            $cooperation = self::find($cooperation);
        }

        if (! $cooperation instanceof \App\Models\Cooperation) {
            return null;
        }

        return \Cache::remember(
            self::getCacheKey(static::CACHE_KEY_GET_STYLE, $cooperation->id),
            config('hoomdossier.cache.times.default'),
            function () use ($cooperation) {
                return $cooperation->style;
            }
        );
    }

    /**
     * get the active steps ordered on the order column.
     *
     * @param int|\App\Models\Cooperation
     *
     * @return mixed
     */
    public static function getActiveOrderedSteps($cooperation): Collection
    {
        if (! $cooperation instanceof \App\Models\Cooperation) {
            $cooperation = self::find($cooperation);
        }

        return \Cache::remember(
            self::getCacheKey(static::CACHE_KEY_GET_ACTIVE_ORDERED_STEPS, $cooperation->id),
            config('hoomdossier.cache.times.default'),
            function () use ($cooperation) {
                return $cooperation
                    ->steps()
                    ->where('steps.parent_id', '=', null)
                    ->orderBy('cooperation_steps.order')
                    ->where('cooperation_steps.is_active', '1')
                    ->get();
            }
        );
    }

    /**
     * Method to forget the cooperation cache with specific key.
     *
     * @param $cacheKey
     * @param $cooperationId
     */
    public static function wipe($cacheKey, $cooperationId)
    {
        \Cache::forget(self::getCacheKey($cacheKey, $cooperationId));
    }
}
