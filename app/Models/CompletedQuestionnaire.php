<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

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
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire forBuilding(\App\Models\Building $building)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedQuestionnaire whereUserId($value)
 * @mixin \Eloquent
 */
class CompletedQuestionnaire extends Model
{
    use GetMyValuesTrait;
    use GetValueTrait;
}
