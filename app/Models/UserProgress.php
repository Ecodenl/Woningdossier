<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserProgress.
 *
 * @property int                                                         $id
 * @property int|null                                                    $input_source_id
 * @property int                                                         $step_id
 * @property int|null                                                    $building_id
 * @property \Illuminate\Support\Carbon|null                             $created_at
 * @property \Illuminate\Support\Carbon|null                             $updated_at
 * @property \App\Models\InputSource|null                                $inputSource
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Step[] $steps
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProgress whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserProgress extends Model
{
    use GetMyValuesTrait;
    use GetValueTrait;

    public $fillable = [
        'user_id', 'step_id', 'building_id', 'input_source_id',
    ];

    public function steps()
    {
        return $this->hasMany(Step::class);
    }
}
