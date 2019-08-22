<?php

namespace App\Helpers\Cache;

class Status
{
    const CACHE_KEY_FIND = 'Status_find_%s';

    /**
     * @param int $id
     *
     * @return \App\Models\Status|null
     */
    public static function find($id)
    {
        return \Cache::remember(
            sprintf(static::CACHE_KEY_FIND, $id),
            config('woningdossier.cache.times.default'),
            function () use ($id) {
                return \App\Models\Status::find($id);
            }
        );
    }
}
