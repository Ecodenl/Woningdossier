<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ComfortLevelTapWater
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComfortLevelTapWater whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ComfortLevelTapWater extends Model
{
    use TranslatableTrait;
}
