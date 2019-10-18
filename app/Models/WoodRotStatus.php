<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WoodRotStatus.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int|null                        $calculate_value
 * @property int                             $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WoodRotStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WoodRotStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WoodRotStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WoodRotStatus translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WoodRotStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WoodRotStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WoodRotStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WoodRotStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WoodRotStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WoodRotStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WoodRotStatus extends Model
{
    use TranslatableTrait;
}
