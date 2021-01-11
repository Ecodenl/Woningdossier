<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FacadeDamagedPaintwork.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int|null                        $calculate_value
 * @property int                             $order
 * @property int|null                        $term_years
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork query()
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereTermYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacadeDamagedPaintwork whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadeDamagedPaintwork extends Model
{
    use TranslatableTrait;
}
