<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SpaceCategory.
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpaceCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SpaceCategory extends Model
{
    use TranslatableTrait;
}
