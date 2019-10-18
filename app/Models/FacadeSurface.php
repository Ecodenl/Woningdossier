<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FacadeSurface.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int|null                        $calculate_value
 * @property int                             $order
 * @property string                          $execution_term_name
 * @property int|null                        $term_years
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface whereExecutionTermName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface whereTermYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeSurface whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadeSurface extends Model
{
    use TranslatableTrait;
}
