<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InterestedToExecuteMeasure
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|InterestedToExecuteMeasure newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InterestedToExecuteMeasure newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InterestedToExecuteMeasure query()
 * @method static \Illuminate\Database\Eloquent\Builder|InterestedToExecuteMeasure whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InterestedToExecuteMeasure whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InterestedToExecuteMeasure whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InterestedToExecuteMeasure whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InterestedToExecuteMeasure whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InterestedToExecuteMeasure extends Model
{
}
