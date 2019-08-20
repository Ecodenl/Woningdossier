<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use TranslatableTrait;

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
