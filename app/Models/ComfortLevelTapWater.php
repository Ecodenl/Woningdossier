<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ComfortLevelTapWater.
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ComfortLevelTapWater newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ComfortLevelTapWater newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ComfortLevelTapWater query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ComfortLevelTapWater translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ComfortLevelTapWater whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ComfortLevelTapWater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ComfortLevelTapWater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ComfortLevelTapWater whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ComfortLevelTapWater whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ComfortLevelTapWater whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ComfortLevelTapWater extends Model
{
    use TranslatableTrait;
}
