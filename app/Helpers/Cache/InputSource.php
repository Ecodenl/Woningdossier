<?php

namespace App\Helpers\Cache;

class InputSource {

    const CACHE_KEY_GET_ORDERED = 'InputSource_getOrdered';

    public static function getOrdered(){
        return \Cache::remember(
            sprintf(static::CACHE_KEY_GET_ORDERED),
            config('woningdossier.cache.times.default'),
            function () {
                return \App\Models\InputSource::orderBy('order', 'desc')->get();
            }
        );
    }
}