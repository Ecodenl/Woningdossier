<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Occupation
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Occupation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Occupation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Occupation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Occupation whereUpdatedAt($value)
 */
class Occupation extends Model
{
    public $fillable = [ 'name', ];
}
