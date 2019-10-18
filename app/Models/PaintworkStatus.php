<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaintworkStatus.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int|null                        $calculate_value
 * @property int                             $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaintworkStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaintworkStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaintworkStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaintworkStatus translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaintworkStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaintworkStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaintworkStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaintworkStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaintworkStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaintworkStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaintworkStatus extends Model
{
    use TranslatableTrait;
}
