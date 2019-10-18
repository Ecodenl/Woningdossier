<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PresentWindow.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int                             $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentWindow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentWindow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentWindow query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentWindow whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentWindow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentWindow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentWindow whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PresentWindow whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PresentWindow extends Model
{
}
