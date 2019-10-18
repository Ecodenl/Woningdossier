<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CompletedQuestionnaire.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int                             $questionnaire_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedQuestionnaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedQuestionnaire newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedQuestionnaire query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedQuestionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedQuestionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedQuestionnaire whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedQuestionnaire whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedQuestionnaire whereUserId($value)
 * @mixin \Eloquent
 */
class CompletedQuestionnaire extends Model
{
}
