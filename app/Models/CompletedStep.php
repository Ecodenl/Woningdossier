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
 * @property \App\Models\Step                                            $step
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Step[] $steps
 * @property int|null                                                    $steps_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep forBuilding(\App\Models\Building $building)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedStep whereUpdatedAt($value)
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
        // not sure if this is used, this cant work
        return $this->hasMany(Step::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class);
    }
}
