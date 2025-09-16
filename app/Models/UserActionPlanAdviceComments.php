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
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActionPlanAdviceComments whereUserId($value)
 * @mixin \Eloquent
 */
class UserActionPlanAdviceComments extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;

    protected $fillable = ['user_id', 'input_source_id', 'comment'];
}
