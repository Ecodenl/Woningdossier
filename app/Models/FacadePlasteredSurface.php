<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FacadePlasteredSurface.
 *
 * @property int $id
 * @property string $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadePlasteredSurface translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadePlasteredSurface whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadePlasteredSurface whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadePlasteredSurface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadePlasteredSurface whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadePlasteredSurface whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadePlasteredSurface whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadePlasteredSurface extends Model
{
    use TranslatableTrait;
}
