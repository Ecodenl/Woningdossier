<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasShortTrait {

    /**
     * Find a record by its short
     *
     * @param $short
     * @return Model
     */
    public static function findByShort($short): Model
    {
        return self::whereShort($short)->first();
    }
}
