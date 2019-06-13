<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Scopes\GetValueScope;
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdviceComments forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdviceComments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdviceComments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdviceComments query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdviceComments whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdviceComments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdviceComments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdviceComments whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdviceComments whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdviceComments whereUserId($value)
 * @mixin \Eloquent
 */
class UserActionPlanAdviceComments extends Model
{
    use GetValueTrait, GetMyValuesTrait;

    protected $fillable = ['user_id', 'input_source_id', 'comment'];

}
