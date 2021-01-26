<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserActionPlanAdviceComments
 *
 * @property int $id
 * @property int $user_id
 * @property int $input_source_id
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments forBuilding(\App\Models\Building $building)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActionPlanAdviceComments whereUserId($value)
 * @mixin \Eloquent
 */
class UserActionPlanAdviceComments extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;

    protected $fillable = ['user_id', 'input_source_id', 'comment'];
}
