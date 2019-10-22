<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InsulatingGlazing.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int|null                        $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InsulatingGlazing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InsulatingGlazing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InsulatingGlazing query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InsulatingGlazing translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InsulatingGlazing whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InsulatingGlazing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InsulatingGlazing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InsulatingGlazing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InsulatingGlazing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InsulatingGlazing extends Model
{
    use TranslatableTrait;
}
