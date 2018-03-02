<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AssessmentType
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssessmentType whereUpdatedAt($value)
 */
class AssessmentType extends Model
{
    //
}
