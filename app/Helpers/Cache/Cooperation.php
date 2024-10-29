<?php

namespace App\Helpers\Cache;

use App\Models\Cooperation as CooperationModel;
use Illuminate\Support\Facades\Cache;

class Cooperation extends BaseCache
{
    const CACHE_KEY_FIND = 'Cooperation_find_%s';
    const CACHE_KEY_GET_STYLE = 'Cooperation_getStyle_%s';

    public static function find(int $id): ?CooperationModel
    {
        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_FIND, $id),
            config('hoomdossier.cache.times.default'),
            function () use ($id) {
                return CooperationModel::where('id', '=', $id)->with('style')->first();
            }
        );
    }

    public static function getStyle($cooperation)
    {
        if (! $cooperation instanceof CooperationModel) {
            $cooperation = self::find($cooperation);
        }

        if (! $cooperation instanceof CooperationModel) {
            return null;
        }

        return Cache::remember(
            self::getCacheKey(static::CACHE_KEY_GET_STYLE, $cooperation->id),
            config('hoomdossier.cache.times.default'),
            function () use ($cooperation) {
                return $cooperation->style;
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
        static::clear(self::getCacheKey($cacheKey, $cooperationId));
    }
}
