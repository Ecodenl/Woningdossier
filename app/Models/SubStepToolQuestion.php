<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubStepToolQuestion
 *
 * @property int $id
 * @property int $order
 * @property int $sub_step_id
 * @property int $tool_question_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepToolQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepToolQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepToolQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepToolQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepToolQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepToolQuestion whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepToolQuestion whereSubStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepToolQuestion whereToolQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepToolQuestion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubStepToolQuestion extends Model
{
    protected $fillable = [
        'order'
    ];
}
