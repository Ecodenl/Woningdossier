<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Motivation.
 *
 * @property int $id
 * @property string $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Motivation extends Model
{
    use TranslatableTrait;
}
