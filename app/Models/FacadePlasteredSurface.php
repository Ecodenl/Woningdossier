<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FacadePlasteredSurface.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int|null                        $calculate_value
 * @property int                             $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface query()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadePlasteredSurface whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadePlasteredSurface extends Model
{
    use TranslatableTrait;
}
