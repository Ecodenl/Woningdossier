<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\QuestionnaireStep
 *
 * @property int $id
 * @property int $questionnaire_id
 * @property int $step_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Questionnaire $questionnaire
 * @property-read \App\Models\Step $step
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireStep query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireStep whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireStep whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireStep whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireStep whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QuestionnaireStep extends Pivot
{
    use HasFactory;

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
}
