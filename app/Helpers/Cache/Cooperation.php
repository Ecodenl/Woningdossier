<?php

namespace App\Helpers\Cache;

class Cooperation
{

    const CACHE_KEY_FIND = 'Cooperation_find_%s';
    const CACHE_KEY_GET_STYLE = 'Cooperation_getStyle_%s';

    /**
     * @param  int  $id
     *
     * @return \App\Models\Cooperation|null
     */
    public static function find($id)
    {
        return \Cache::remember(
            sprintf(static::CACHE_KEY_FIND, $id),
            config('woningdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\Cooperation::where('id', '=', $id)->with('style')->first();
            }
        );
    }

    public static function getStyle($cooperation)
    {
        if (!$cooperation instanceof \App\Models\Cooperation){
            $cooperation = self::find($cooperation);
        }

        if (!$cooperation instanceof \App\Models\Cooperation){
            return null;
        }

        return \Cache::remember(
            sprintf(static::CACHE_KEY_GET_STYLE, $cooperation->id),
            config('woningdossier.cache.times.default'),
            function () use ($cooperation) {
                return $cooperation->style;
            }
        );
    }

}