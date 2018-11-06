<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputSource extends Model
{
    const RESIDENT_SHORT = "resident";
    const RESIDENT_COACH = "coach";

    public static function findByShort($short)
    {
        return self::where('short', $short)->first();
    }

    /**
     * Check if the input source is a resident
     *
     * @return bool
     */
    public function isResident(): bool
    {
        if ($this->short == "resident") {
            return true;
        }

        return false;
    }
}
