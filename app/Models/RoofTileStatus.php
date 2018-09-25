<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoofTileStatus.
 *
 * @property int $id
 * @property string $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofTileStatus translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofTileStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofTileStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofTileStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofTileStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofTileStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RoofTileStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoofTileStatus extends Model
{
    use TranslatableTrait;
}
