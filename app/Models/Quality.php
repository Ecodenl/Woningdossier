<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Quality
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quality whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Quality whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Quality extends Model
{
    //
}
