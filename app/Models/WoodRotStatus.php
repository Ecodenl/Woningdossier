<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WoodRotStatus
 *
 * @property int $id
 * @property string $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WoodRotStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WoodRotStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WoodRotStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|WoodRotStatus translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|WoodRotStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WoodRotStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WoodRotStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WoodRotStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WoodRotStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WoodRotStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WoodRotStatus extends Model
{
    use TranslatableTrait;
}
