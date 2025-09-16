<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Reason
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason query()
 * @mixin \Eloquent
 */
class Reason extends Model
{
    public $fillable = ['name'];
}
