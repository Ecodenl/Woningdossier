<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Source
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Source whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Source whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Source whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Source whereUpdatedAt($value)
 */
class Source extends Model
{
    public $fillable = ['name', ];
}
