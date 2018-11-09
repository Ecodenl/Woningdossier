<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputSource extends Model
{
    public static function findByShort($short)
    {
        return self::where('short', $short)->first();
    }
}
