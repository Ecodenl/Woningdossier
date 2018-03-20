<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Reason
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reason whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reason whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reason whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reason whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Reason extends Model
{
    public $fillable = [ 'name', ];
}
