<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InsulatingGlazing
 *
 * @property int $id
 * @property string $name
 * @property int|null $calculate_value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
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
