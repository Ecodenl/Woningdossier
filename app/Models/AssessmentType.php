<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AssessmentType.
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AssessmentType extends Model
{
    use TranslatableTrait;
}
