<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Interest.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int                             $calculate_value
 * @property int                             $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Interest extends Model
{
    use TranslatableTrait;


    public function users()
    {
        return $this->morphedByMany(User::class, 'interest', 'user_interests');
    }

    public function steps()
    {
        return $this->morphedByMany(Step::class, 'interest', 'user_interests');
    }
}
