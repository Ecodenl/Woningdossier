<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Reason.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reason query()
 * @mixin \Eloquent
 */
class Reason extends Model
{
    public $fillable = ['name'];
}
