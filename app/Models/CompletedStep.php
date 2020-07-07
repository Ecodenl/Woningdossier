<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CompletedStep.
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedStep whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CompletedStep extends Model
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
