<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoofTileStatus.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int|null                        $calculate_value
 * @property int                             $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoofTileStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoofTileStatus extends Model
{
    use TranslatableTrait;
}
