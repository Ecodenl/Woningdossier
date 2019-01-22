<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FacadeDamagedPaintwork.
 *
 * @property int $id
 * @property string $name
 * @property int|null $calculate_value
 * @property int $order
 * @property int|null $term_years
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork whereTermYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FacadeDamagedPaintwork whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacadeDamagedPaintwork extends Model
{
    use TranslatableTrait;
}
