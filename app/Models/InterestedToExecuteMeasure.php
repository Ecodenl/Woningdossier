<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InterestedToExecuteMeasure.
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InterestedToExecuteMeasure whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InterestedToExecuteMeasure whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InterestedToExecuteMeasure whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InterestedToExecuteMeasure whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InterestedToExecuteMeasure whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InterestedToExecuteMeasure extends Model
{
}
