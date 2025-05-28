<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\CompletedQuestionnaire
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $input_source_id
 * @property int $questionnaire_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompletedQuestionnaire whereUserId($value)
 * @mixin \Eloquent
 */
class CompletedQuestionnaire extends Pivot
{
    protected $table = 'completed_questionnaires';

    use GetMyValuesTrait,
        GetValueTrait;
}
